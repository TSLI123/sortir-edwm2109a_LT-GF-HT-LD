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
    private $entityManager;

    /**
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/participant/profil/{id}", name="profil")
     */
    public function profil(int $id, ParticipantRepository $participantRepository, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $participant = $participantRepository->find($id);

        if (!$participant){
            throw $this->createNotFoundException('Ouuups pas de profil');
        }

        $form = $this->createForm(ProfilType::class, $participant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /*$old_mdp = $form->get('old_motPasse')->getData();

            if ($passwordHasher->isPasswordValid($participant, $old_mdp)){
                $new_mdp = $form->get('new_motPasse')->getData();
                $motPasse = $passwordHasher->hashPassword($participant, $new_mdp);

                $participant->setMotPasse($motPasse);


                $this->entityManager->persist($participant);
                $this->entityManager->flush();

            }*/


            $participant = $form->getData();

            $motPasse = $passwordHasher->hashPassword($participant,$participant->getMotPasse());
            $participant->setMotPasse($motPasse);

            $this->entityManager->persist($participant);
            $this->entityManager->flush();

        }

        return $this->render('participant/profil.html.twig', [
            "participant" => $participant,
            'form' => $form->createView()
            ]);
    }
}
