<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieu", name="lieu_create")
     */
    public function createPlace(Request $request, EntityManagerInterface $entityManager
                                ): Response
    {

        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm-> handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('success', "le lieu a bien été ajouté");
        }

        return $this->render('lieu/index.html.twig', [
            'lieuForm' => $lieuForm->createView(),
        ]);
    }
}
