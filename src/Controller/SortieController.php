<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CreateSortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form;



class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager ,ParticipantRepository $participantRepository): Response

    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {


            if($sortieForm->get('save')->isClicked()){


               $etat = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Créée']);
               $this->addFlash('success',"la sortie a été créée"); }
            else{
                $etat=$entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Ouverte']);
                $this->addFlash('success',"la sortie a été publié");
            }
          //  $currentUserUsername = $this->getUser()->getUserIdentifier();

            //$sortie->setOrganisateur($currentUserUsername);
         $organisateur = $participantRepository->find($this->getUser()->getUserIdentifier());

            $sortie->setEtat($etat);
           $sortie->setOrganisateur($organisateur);



            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('affichage.html.twig');
        }


        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}
