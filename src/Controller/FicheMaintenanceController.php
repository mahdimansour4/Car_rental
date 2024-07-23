<?php

namespace App\Controller;


use App\Entity\FicheMaintenance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FicheMaintenanceController extends AbstractController{

    #[Route('/voiture/{id}/fiche-maintenance', name: 'ficheMaintenance')]
    public function create(Request $request,EntityManagerInterface $em,$id): Response{
        if($request->isMethod("POST")){
            $ficheMaintenance = new FicheMaintenance();
            $ficheMaintenance->setVoiture($em->getRepository(Voiture::class)->find($id));
            $ficheMaintenance->setDate(new \DateTime($request->get("date")));
            $ficheMaintenance->setType($request->get("type"));
            $ficheMaintenance->setKilometrage($request->get("kilometrage"));
            $ficheMaintenance->setDescription($request->get("description"));
            $em->persist($ficheMaintenance);
            $em->flush();
            return $this->redirectToRoute('fiche_maintenance');
        }
        return $this->render('car/fiche_maintenance.html.twig',[
            'id' => $id
        ]);
    }
}