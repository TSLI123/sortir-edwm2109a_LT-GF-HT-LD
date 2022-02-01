<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

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


        return $this->render('admin/create.html.twig', [
            'participantForm' => $participantForm->createView()
        ]);
    }
}
