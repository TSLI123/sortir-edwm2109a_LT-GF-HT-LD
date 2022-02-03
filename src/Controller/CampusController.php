<?php

namespace App\Controller;

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
    public function gererCampus(CampusRepository $campusRepository,Request $request):Response
    {

        $campusform = $this->createForm(CampusType::class);
        $campusform->handleRequest($request);
        if ($campusform->isSubmitted() && $campusform->isValid()){
            $cri = $campusform->getData();

            $campus = $campusRepository->findByCampus($cri);
        }
        else{

            $campus = $campusRepository->findAll();
        }
        return $this->render('campus/gererCampus.html.twig',[
            'campusForm' => $campusform->createView(),
            'campuss' =>$campus
        ]);
    }
    /**
     * @Route ("/supprimer/{id}" , name="supprimer")
     *
     */

    public function supprimerCampus(int $id,CampusRepository $campusRepository , EntityManagerInterface $entityManager , ParticipantRepository $participantRepository)
    {
        $campus = $campusRepository->find($id);

        if (!$campus){
            throw $this->createNotFoundException('Campus  pas trouvé');
        }
        else{
            $entityManager->remove($campus);
            $entityManager->flush();
        }
        return $this->redirectToRoute('campus_gererCampus');

    }
}
