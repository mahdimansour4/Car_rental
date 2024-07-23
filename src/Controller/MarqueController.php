<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Repository\MarqueRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MarqueController extends AbstractController{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository,
        private readonly MarqueRepository $marqueRepository
    ) {
    }

    #[Route('/marque_create', name: 'marque.create')]
    public function createMarque(Request $request,EntityManagerInterface $em) : Response {
        $user = $this->userRepository->findUserByEmailOrUsername($this->getUser()->getUserIdentifier());
        $id = $user->getProfile()->getId();
        $marques = $this->marqueRepository->findAll();

        if ($role = $this->roleRepository->getRole($id, 'ADMIN')) {
            if ($request->isMethod("POST")) {
                $marque = new Marque();
                $marque->setNom($request->get('nom'));
                $em->persist($marque);
                $em->flush();
                return $this->redirectToRoute('admin');
            }
            return $this->render('marque/create.html.twig',[
                'marques' => $marques,
                'isAdmin' => true,
            ]);
        }
        $this->addFlash("Vous n'avez pas l'acces a cette page");
        return $this->redirectToRoute('home',);
    }
}