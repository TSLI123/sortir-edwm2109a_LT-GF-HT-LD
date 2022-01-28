<?php

namespace App\Controller;

use App\Classes\FiltresSorties;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\CreateSortieType;
use App\Form\FiltresSortiesType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response

    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {


            if ($sortieForm->get('save')->isClicked()) {


                $etat = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']);
                $this->addFlash('success', "la sortie a été créée");
            } else {
                $etat = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
                $this->addFlash('success', "la sortie a été publié");
            }

            $organisateur = $participantRepository->find($this->getUser());

            $sortie->setEtat($etat);
            $sortie->setOrganisateur($organisateur);


            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_accueil');
        }


        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    /**
     * @Route("/accueil", name="accueil")
     */
    public function index(Request $request, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        $filtre = new FiltresSorties();

        $sortieForm = $this->createForm(FiltresSortiesType::class, $filtre);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sorties = $sortieRepository->findByData($filtre, $this->getUser());

        } else {
            $sorties = $sortieRepository->findAllCurrentSorties();
        }

        // recherche et clotures des sorties
        $currentTime = new \DateTime();
        foreach ($sorties as $sortie){
            if ($sortie->getEtat()->getLibelle() === 'Ouverte' && $sortie->getDateLimiteInscription() < $currentTime){
                $etat = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }
        }

        // recherche et passage en "passée" avec archivage automatique
        foreach ($sorties as $sortie){
            $dateSortie = new \DateTime($sortie->getDateHeureDebut()->format("d-m-Y"));
            if (date_add($dateSortie, date_interval_create_from_date_string("30 days")) < $currentTime){
                $etat = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'passée']);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }
        }




        return $this->render('sortie/accueil.html.twig', [
            'sorties' => $sorties,
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    //Afficher les infos d'une sortie
    /**
     * @Route ("/details/{id}", name="details")
     */
    public function details(int $id, SortieRepository $sortieRepository):Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie){
            throw  $this->createNotFoundException('Aucune sortie par ici !');
        }
        return $this->render('sortie/details.html.twig', [
            "sortie" => $sortie
        ]);

    }

}
