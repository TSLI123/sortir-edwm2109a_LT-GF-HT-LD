<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\AddCampusType;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/campus", name="campus_")
 */
class CampusController extends AbstractController
{

    /**
     * @Route ("/" , name="gererCampus")
     */
    public function gererCampus(CampusRepository $campusRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $campu = new Campus();
        $addForm = $this->createForm(AddCampusType::class, $campu);
        $campusform = $this->createForm(CampusType::class);
        $campusform->handleRequest($request);
        $addForm->handleRequest($request);
        if ($campusform->isSubmitted() && $campusform->isValid()) {
            $cri = $campusform->getData();

            $campus = $campusRepository->findByCampus($cri);

        } else {

            $campus = $campusRepository->findAll();
        }
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $campu=$addForm->getData();
            $entityManager->persist($campu);
            $entityManager->flush();
            $this->addFlash('success', ('Campus de "' . $campu->getNom() . '" ajoutée !'));
            return $this->redirectToRoute('campus_gererCampus');
        }
            return $this->render('admin/gererCampus.html.twig', [
                'campusForm' => $campusform->createView(),
                'addForm' => $addForm->createView(),
                'campuss' => $campus
            ]);

    }

    /**
     * @Route ("/supprimer/{id}" , name="supprimer")
     *
     */

    public function supprimerCampus(int $id, CampusRepository $campusRepository, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository)
    {
        $campus = $campusRepository->find($id);

        if (!$campus) {
            throw $this->createNotFoundException('Campus  pas trouvé');
        } else {
            $entityManager->remove($campus);
            $entityManager->flush();
        }
        return $this->redirectToRoute('campus_gererCampus');

    }

    /**
     * @Route ("/add" , name="add")
     */
    public function addCampus(EntityManagerInterface $entityManager, Request $request): Response
    {
        $campu = new Campus();
        $addForm = $this->createForm(AddCampusType::class, $campu);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $entityManager->persist($campu);
            $entityManager->flush();
            $this->addFlash('success', ('Campus de "' . $campus->getNom() . '" ajoutée !'));
            return $this->redirectToRoute('campus_gererCampus');
        }


        return $this->render('admin/gererCampus.html.twig', [
            'addForm' => $addForm->createView()
        ]);
    }

    /**
     * @Route ("/update/{id}" , name="update")
     */
    public function modifierCampus(int $id, EntityManagerInterface $entityManager, Request $request, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->find($id);
        $modifierCampus = $this->createForm(AddCampusType::class, $campus);
        $modifierCampus->handleRequest($request);
        if ($modifierCampus->isSubmitted() && $modifierCampus->isValid()) {
            $campus = $modifierCampus->getData();
            $entityManager->flush();
            $this->addFlash('success', ('Campus de "' . $campus->getNom() . '" modifiée !'));
            return $this->redirectToRoute('campus_gererCampus');
        }
        return $this->render('admin/modifyCampus.html.twig', [
            'modifiCampus' => $modifierCampus->createView()
        ]);
    }

}
