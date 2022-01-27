<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/profil/{pseudo}", name="profil")
     */
    public function profil(string $pseudo, ParticipantRepository $participantRepository, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        dump($user);

        if ($pseudo !== $user->getPseudo()){
            $profil = $participantRepository->loadUserByIdentifier($pseudo);
            if (!$profil) {
                throw $this->createNotFoundException('Ouuups pas de profil');
            }
            return $this->render('participant/profil.html.twig', [
                'profil' => $profil
            ]);
        }

        if (!$user){
            throw $this->createNotFoundException('Ouuups pas de profil');
        }

        $form = $this->createForm(ProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){


            $motPasse = $form->get('new_motPasse')->getData();

            if (empty($motPasse)){

                $participant = $form->get('pseudo', 'nom', 'prenom','telephone','email','campus')->getData();

                $participant = $form->getData();


           }
            else {
                $participant = $form->getData();
                $motPasse = $passwordHasher->hashPassword($participant, $motPasse);
                $participant->setMotPasse($motPasse);
                $this->entityManager->persist($participant);

            }

            $this->entityManager->flush();

            //Créer un message à afficher à l'issue
            $this->addFlash('success', 'Profil mis à jour !');

        }

        return $this->render('participant/myProfil.html.twig', [
            'form' => $form->createView()
            ]);
    }
}
