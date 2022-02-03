<?php

namespace App\Controller;

use App\Classes\FiltresVilles;
use App\Entity\Ville;
use App\Form\CreateVilleType;
use App\Form\FiltresVillesType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CitiesController extends AbstractController
{
    //Gérer les villes
    /**
     * @Route("/cities", name="manage_cities")
     */
    public function manageCities(VilleRepository $villeRepository, Request $request, EntityManagerInterface  $entityManager) : Response
    {

        $filtre = new FiltresVilles();
        $city = new Ville();

        $cityForm = $this->createForm(CreateVilleType::class, $city);

        $filtreVillesForm = $this->createForm(FiltresVillesType::class, $filtre);
        $filtreVillesForm->handleRequest($request);

        if ($filtreVillesForm->isSubmitted() && $filtreVillesForm->isValid()) {

            $cities = $villeRepository->findByData($filtre);

            return $this->render('admin/cities.html.twig', [
                "cities" => $cities,
                "cityForm" => $cityForm->createView(),
                "filtreVilleform" =>$filtreVillesForm->createView(),
            ]);

        }



        $cities = $villeRepository->findAll();

        $cityForm->handleRequest($request);

        if ($cityForm->isSubmitted() && $cityForm->isValid()){
            $city = $cityForm->getData();
            $entityManager->persist($city);
            $entityManager->flush();

            //Créer un message à afficher à l'issue
            $this->addFlash('success', ('Ville de '.$city->getNom().' ajoutée !'));

            return $this->redirectToRoute('manage_cities');
        }




        if (!$cities) {
            throw  $this->createNotFoundException('Aucunes villes');
        }

        return $this->render('admin/cities.html.twig', [
            "cities" => $cities,
            "cityForm" => $cityForm->createView(),
            "filtreVilleform" =>$filtreVillesForm->createView()
        ]);
    }
    //Supprime une ville
    /**
     * @Route("/cities/remove/{id}", name="remove_city")
     */
    public function removeCity(int $id, VilleRepository $villeRepository, EntityManagerInterface $entityManager) :RedirectResponse
    {
        $city = $villeRepository->find($id);

        $entityManager->remove($city);
        $entityManager->flush();
        $this->addFlash('success', ($city->getNom()." à été supprimée"));


        return $this->redirectToRoute('manage_cities');
    }
}
