<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Form\CsvType;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/create", name="create_participant")
     */
    public function createParticipant(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $participant = new Participant();
        $participant->setActif(true);

        // Pour le moment on utilise un mot de passe par défaut
        // il faudra utiliser un generateur de mot de passe et l'envoyer par email
        $plainPassword = "root123456";
        $participant->setMotPasse($userPasswordHasher->hashPassword($participant, $plainPassword));

        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() && $participantForm->isValid()) {

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', "Participant créé");
        }

        // Formulaire d'upload de fichier

        $fileForm = $this->createForm(CsvType::class);
        $fileForm->handleRequest($request);
        if ($fileForm->isSubmitted() && $fileForm->isValid()) {
            $file = $fileForm['attachment']->getData();

            if ($file) {
                try {
                    $datas = $this->decode($file->getPathname());
                    foreach ($datas as $data) {
                        dump($data);
                        $part = new Participant();
                        $part->setActif(true)
                            ->setMotPasse($userPasswordHasher->hashPassword($part, $plainPassword))
                            ->setPseudo($data['pseudo'])
                            ->setPrenom($data['prenom'])
                            ->setNom($data['nom'])
                            ->setEmail($data['email'])
                            ->setTelephone($data['telephone'])
                            ->setCampus($entityManager->getRepository(Campus::class)->findOneBy(['nom' => $data['campus']]))
                            ->setAdministrateur(false);
                        $entityManager->persist($part);
                    }
                    $entityManager->flush();
                    $this->addFlash('success', "importation réussie");
                } catch (\Doctrine\DBAL\Exception $e) {
                    $this->addFlash('failure', "Echec de l'import de participants");
                } catch (ErrorException $e2) {
                    $this->addFlash('failure', "Fichier non conforme");
                }
            }

        }

        return $this->render('admin/create.html.twig', [
            'participantForm' => $participantForm->createView(),
            'fileForm' => $fileForm->createView()
        ]);
    }

    private function decode($csvPath)
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        return $serializer->decode(file_get_contents($csvPath), 'csv', [CsvEncoder::DELIMITER_KEY => ';']);
    }

}
