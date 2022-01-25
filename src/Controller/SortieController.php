<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreateSortieType;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_create")
     */
    public function create(Request $request): Response

    {
        $sortie = new Sortie();
        $sortieForm =$this->createForm(CreateSortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        return $this->render('sortie/create.html.twig' ,[
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}
