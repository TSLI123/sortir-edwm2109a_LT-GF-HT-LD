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
     * @Route("/profil/{pseudo}", name="profil")
     */
    public function profil(string $pseudo, ParticipantRepository $participantRepository, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = $this->getUser();

        if (!$user){
            throw $this->createNotFoundException('Ouuups pas de profil');
        }

        $form = $this->createForm(ProfilType::class, $user);

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

            //$this->entityManager->persist($participant);
            $this->entityManager->flush();

            //Créer un message à afficher à l'issue
            $this->addFlash('success', 'Profil mis à jour !');

        }

        return $this->render('participant/profil.html.twig', [
            "app.user" => $user,
            'form' => $form->createView()
            ]);
    }
}
