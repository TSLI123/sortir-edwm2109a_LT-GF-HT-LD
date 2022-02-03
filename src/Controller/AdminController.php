<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Form\CsvType;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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


    //Page de gestion des participants par l'administrateur
    /**
     * @Route("/manage", name="manage_participant")
     */
    public function manageParticipant(ParticipantRepository $participantRepository) :Response
    {
        $participants = $participantRepository->findAll();

        if (!$participants) {
            throw  $this->createNotFoundException('Aucun participants.');
        }

        return $this->render('admin/manage.html.twig', [
            "participants" => $participants
        ]);

    }
    //Désactive un participant
    /**
     * @Route("/manage/disable/{id}", name="disable_participant")
     */
    public function disableParticipant(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager) :RedirectResponse
    {
        $participant = $participantRepository->find($id);
        $participant->setActif(0);

        $entityManager->persist($participant);
        $entityManager->flush();

        $this->addFlash('success', "Le participant est désormais 'Désactivé'.");

        return $this->redirectToRoute('admin_manage_participant');
    }
    //Active un participant
    /**
     * @Route("/manage/active/{id}", name="active_participant")
     */
    public function activeParticipant(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager) :RedirectResponse
    {
        $participant = $participantRepository->find($id);
        $participant->setActif(1);

        $entityManager->persist($participant);
        $entityManager->flush();

        $this->addFlash('success', "Le participant est désormais 'Actif'.");

        return $this->redirectToRoute('admin_manage_participant');
    }
    //Supprime un participant
    /**
     * @Route("/manage/remove/{id}", name="remove_participant")
     */
    public function removeParticipant(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager) :RedirectResponse
    {
        $participant = $participantRepository->find($id);

        $entityManager->remove($participant);
        $entityManager->flush();
        $this->addFlash('success', "Le participant à été supprimé");


        return $this->redirectToRoute('admin_manage_participant');
    }




}
