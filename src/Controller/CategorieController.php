<?php

namespace App\Controller;


use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategorieController extends  AbstractController{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository,
    ) {
    }

    #[Route('/categorie_create', name: 'categorie.create')]
    public function createMarque(Request $request,EntityManagerInterface $em):Response
    {
        $user = $this->userRepository->findUserByEmailOrUsername($this->getUser()->getUserIdentifier());
        $id = $user->getProfile()->getId();
        $categories = $em->getRepository(Categorie::class)->findAll();

        if($role = $this->roleRepository->getRole($id, 'ADMIN')){
            if($request->isMethod('POST')){
                $categorie = new Categorie();
                $categorie->setNom($request->get('nom'));
                $em->persist($categorie);
                $em->flush();
                return $this->redirectToRoute('admin');
            }
            return $this->render('categorie/create.html.twig', [
                'categories' => $categories,
            ]);
        }
        return $this->redirectToRoute('home', [], Response::HTTP_BAD_REQUEST);
    }
}