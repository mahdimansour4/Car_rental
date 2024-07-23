<?php

namespace App\Controller;

use App\Entity\PapierVoiture;
use App\Entity\Voiture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PapierVoitureController extends AbstractController{

    #[Route('/voiture/{id}/papiervoiture', name: 'papiervoiture')]
    public function createPapier(Request $request,EntityManagerInterface $em,$id): Response
    {
        $user = $this->userRepository->findUserByEmailOrUsername($this->getUser()->getUserIdentifier());
        $idrole = $user->getProfile()->getId();
        if ($role = $this->roleRepository->getRole($idrole, 'ADMIN')) {
            if ($request->isMethod("POST")) {
                $papierVoiture = new PapierVoiture();
                $papierVoiture->setVoiture($em->getRepository(Voiture::class)->find($id));
                $papierVoiture->setDateFinAssurance(new \DateTime($request->get("dateFinAssurance")));
                $papierVoiture->setPrixAssurance($request->get("prixAssurance"));
                $papierVoiture->setDateFinVignette(new \DateTime($request->get("dateFinVignette")));
                $papierVoiture->setPrixVignette($request->get("prixVignette"));
                $em->persist($papierVoiture);
                $em->flush();
                return $this->redirectToRoute('admin');
            }
            return $this->render('car/papier.html.twig');
        }
        $this->addFlash("Vous n'avez pas l'acces a cette page");
        return $this->redirectToRoute('home',);
    }


}