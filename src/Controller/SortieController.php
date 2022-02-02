<?php

namespace App\Controller;

use App\Classes\FiltresSorties;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\CreateSortieType;
use App\Form\FiltresSortiesType;
use App\Form\SortieAnnulerType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     *
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
        foreach ($sorties as $sortie) {
            if ($sortie->getEtat()->getLibelle() === 'Ouverte' && $sortie->getDateLimiteInscription() < $currentTime) {
                $etat = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }
        }

        // recherche et passage en "passée" avec archivage automatique
        foreach ($sorties as $sortie) {
            $dateSortie = new \DateTime($sortie->getDateHeureDebut()->format("d-m-Y"));
            if (date_add($dateSortie, date_interval_create_from_date_string("30 days")) < $currentTime) {
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
    public function details(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            throw  $this->createNotFoundException('Aucune sortie par ici !');
        }
        return $this->render('sortie/details.html.twig', [
            "sortie" => $sortie
        ]);

    }


    /**
     * @Route ("/annuler/{id}" , name="annuler")
     */
    public function annuler(int $id, SortieRepository $sortieRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw  $this->createNotFoundException('Aucune sortie par ici !');
        }

        $annulerForm = $this->createForm(SortieAnnulerType::class, $sortie);
        $annulerForm->handleRequest($request);
        if ($annulerForm->isSubmitted() && $annulerForm->isValid()) {
            $etat = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Annulée']);

            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_accueil');
        }

        return $this->render('sortie/annuler.html.twig', [
            "sortie" => $sortie,
            'annulerForm' => $annulerForm->createView()
        ]);
    }

    /**
     * @Route ("/inscription/{id}" , name="sInscrire")
     */
    public function sInsrireAUneSortie(int $id, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): RedirectResponse
    {

        $sortie = $sortieRepository->find($id);
        $statut = $sortie->getEtat()->getLibelle();

        if ($statut == 'Ouverte' && ($sortie->getNbInscriptionsMax() != $sortie->getParticipants()->count())) {

            $participant = $participantRepository->find($this->getUser());

            //ajouter le participant s'il n'est pas déjà dans la liste
            $sortie->addParticipant($participant);

            if ($sortie->getNbInscriptionsMax() == $sortie->getParticipants()->count()) {
                $sortie->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']));
            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', "Super vous êtes incrit à la sortie!");
        } else {
            throw $this->createNotFoundException('Ouuups les inscriptions ne sont pas encore ouvertes');
        }

        return $this->redirectToRoute("sortie_accueil");
    }


    /**
     * @Route ("/publication/{id}" , name="publierSortie")
     */
    public function publierSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): RedirectResponse
    {
        //récupérer la sortie
        $sortie = $sortieRepository->find($id);
        $organisateur = $sortie->getOrganisateur();
        $user = $this->getUser();

        //conditionner la publication à l'organisateur
        if ($organisateur === $user) {
            //la sortie prend le statut 'ouverte'
            $etat = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
            $sortie->setEtat($etat);

            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', "la sortie a été publiée");
        } else {
            throw $this->createNotFoundException('Ouuups vous n êtes pas l organisateur de cette sortie');
        }
        return $this->redirectToRoute("sortie_accueil");
    }

    /**
     * @Route ("/desinscription/{id}" , name="seDesister")
     */
    public function seDesisteDUneSortie(int $id, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): RedirectResponse
    {
        $sortie = $sortieRepository->find($id);

        // tant que la sortie n'a pas commencée
        if ($sortie->getDateHeureDebut() > new \DateTime()) {

            $participant = $participantRepository->find($this->getUser());

            $sortie->removeParticipant($participant);
            if ($sortie->getEtat()->getLibelle() == 'Clôturée') {
                $sortie->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']));
            }
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', "Vous vous êtes désinscrit");
        }

        return $this->redirectToRoute("sortie_accueil");
    }

    /**
     * @Route ("/modifier/{id}" , name="modifier")
     */
    public function modifierSortie(int $id, SortieRepository $sortieRepository,Request $request,
                                   EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);

        //conditionner la modification à l'organisateur et aux sorties non publiées
        if ($sortie->getOrganisateur() !== $this->getUser() || $sortie->getEtat()->getLibelle() != 'Créée') {
            return $this->redirectToRoute("sortie_accueil");
        }
        $sortieForm = $this->createForm(CreateSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        return $this->render('sortie/modify.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }


}
