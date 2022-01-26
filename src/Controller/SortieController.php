<?php

namespace App\Controller;

use App\Form\ListSortieType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/accueil", name="sortir_accueil")
     */
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();

        $sortieForm = $this->createForm(ListSortieType::class);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $sortieForm->getData();
            //var_dump($data);
        }

        return $this->render('sortie/accueil.html.twig', [
            'sorties' => $sorties,
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}
