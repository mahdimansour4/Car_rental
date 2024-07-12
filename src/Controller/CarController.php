<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Marque;
use App\Entity\Reservation;
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
                $voiture->setMarque($em->getRepository(Marque::class)->find($request->get("marque")));
                $voiture->setCategorie($em->getRepository(Categorie::class)->find($request->get("categorie")));
                $voiture->setStatutReservation('false');
                $file = $request->files->get('image');
                if ($file) {
                    $fileName = $fileUploaderService->upload($file);
                    $image->setImagePath($fileName);
                }
                $voiture->addImage($image);
                $em->persist($image);
                $em->persist($voiture);
                $em->flush();
                $this->addFlash('success',"La voiture a ete ajoute");
                return $this->redirectToRoute('admin');
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
    public function carList(Request $request,EntityManagerInterface $em,ImageRepository $imageRepository): Response
    {
        $voitures = $em->getRepository(Voiture::class)->findAllNotBooked();
        $images = $imageRepository->findAll();
        return $this->render('car/car_list.html.twig',[
            'voitures' => $voitures,
            'images' => $images,
        ]);
    }

    #[Route('/car_show/{id}', name: 'car.show')]
    public function carShow(Request $request,EntityManagerInterface $em,$id){
        $voiture= $em->getRepository(Voiture::class)->find($id);
        $image = $em->getRepository(Image::class)->findImageByCar($id);
        return $this->render('car/car.html.twig',[
            'voiture' => $voiture,
            'image' => $image,
        ]);
    }

    public function CarReturned(Request $request,EntityManagerInterface $em,$id){
        $reservation = $em->getRepository(Reservation::class)->find($id);
        $voiture = $reservation->getVoiture();
        $voiture->setStatutReservation(0);
        $voiture->setLieu($reservation->getLieuRetour());
        $em->persist($voiture);
        $em->flush();
        return $this->redirectToRoute('admin');
    }

    #[Route('/car_search/', name: 'car.search')]
    public function search(Request $request,EntityManagerInterface $em){
        if($request->isMethod("GET")){
            $voitures = $em->getRepository(Voiture::class)->findByQuery($request->get('q'));
            $images = $em->getRepository(Image::class)->findAll();
            return $this->render('car/car_list.html.twig',[
                'voitures' => $voitures,
                'images' => $images,
            ]);
        }
        return $this->render('car/car.html.twig',[
            'voitures' => $em->getRepository(Voiture::class)->findAll(),
        ]);
    }
}