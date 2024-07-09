<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Entity\Image;
use App\Repository\CategorieRepository;
use App\Repository\ImageRepository;
use App\Repository\MarqueRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\VoitureRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository,
        private readonly MarqueRepository $marqueRepository,
        private readonly CategorieRepository $categoryRepository,
    ) {
    }

    #[Route('/car_create', name: 'car.create')]
    public function Create(Request $request,
                           FileUploaderService $fileUploaderService,
                           EntityManagerInterface $em):Response{
        $user = $this->userRepository->findUserByEmailOrUsername($this->getUser()->getUserIdentifier());
        $id = $user->getProfile()->getId();
        $marques = $this->marqueRepository->findAll();
        $categories = $this->categoryRepository->findAll();
        if($role = $this->roleRepository->getRole($id, 'ADMIN')) {

            if ($request->isMethod("POST")) {
                $voiture = new Voiture();
                $image = new Image();
                $voiture->setModele($request->get("modele"));
                $voiture->setCouleur($request->get("couleur"));
                $voiture->setLieu($request->get("lieu"));
                $voiture->setAttributs($request->get("attributs"));
                $voiture->setPrixParJour($request->get("prixParJour"));
                $voiture->setCreatedAt(new \DateTimeImmutable('now'));
                $voiture->setMarque($request->get("marque"));
                $voiture->setCategorie($request->get("categorie"));
                $file = $request->files->get('image');
                if ($file) {
                    $fileName = $fileUploaderService->upload($file);
                    $image->setImagePath($fileName);
                }
                $voiture->addImage($image);
                $em->persist($image);
                $em->persist($voiture);
                $em->flush();
                return $this->redirectToRoute('home');
            }

            return $this->render('car/create.html.twig',[
                'marques' => $marques,
                'categories' => $categories,
            ]);
        }else{
            return $this->redirectToRoute('home', [], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/car_list', name: 'car.list')]
    public function carList(Request $request, VoitureRepository $voitureRepository,ImageRepository $imageRepository): Response
    {
        $voitures = $voitureRepository->findAll();
        $images = $imageRepository->findAll();
        return $this->render('car/car.html.twig',[
            'voitures' => $voitures,
            'images' => $images,
        ]);
    }
}