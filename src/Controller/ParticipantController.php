<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilType;
use App\Repository\ParticipantRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/profil/{pseudo}", name="participant_profil")
     */
    public function profil(string $pseudo, ParticipantRepository $participantRepository, Request $request, UserPasswordHasherInterface $passwordHasher, FileUploader $file_uploader, Participant $participant): Response
    {



        $user = $this->getUser();

        if ($pseudo !== $user->getPseudo()){
            $profil = $participantRepository->loadUserByIdentifier($pseudo);
            if (!$profil) {
                throw $this->createNotFoundException('Ouuups pas de profil');
            }
            return $this->render('participant/profil.html.twig', [
                'profil' => $profil
            ]);
        }


        $form = $this->createForm(ProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){


            if (!empty($form->get('imgProfil')->getData()))
            {
                $file = $form['imgProfil']->getData();
                $file_name = $file_uploader->upload($file, $participant);
                $participant->setImgProfil($file_name);
            }

            $motPasse = $form->get('new_motPasse')->getData();

            $participant = $form->getData();
            if (!empty($motPasse)) {
                $motPasse = $passwordHasher->hashPassword($participant, $motPasse);
                $participant->setMotPasse($motPasse);

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

