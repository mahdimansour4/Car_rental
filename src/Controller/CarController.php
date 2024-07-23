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
use App\Service\FileUploaderService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
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
        $idrole = $user->getProfile()->getId();
        $marques = $this->marqueRepository->findAll();
        $categories = $this->categoryRepository->findAll();
        if($this->roleRepository->getRole($idrole, 'ADMIN')) {

            if ($request->isMethod("POST")) {
                $voiture = new Voiture();
                $image = new Image();
                $voiture->setModele($request->get("modele"));
                $voiture->setCouleur($request->get("couleur"));
                $voiture->setLieu($request->get("lieu"));
                $voiture->setAttributs($request->get("attributs"));
                $voiture->setPrixParJour($request->get("prixParJour"));
                $voiture->setCreatedAt(new DateTimeImmutable('now'));
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
                return $this->redirectToRoute('papierVoiture',[
                    'id' => $voiture->getId()
                ]);
            }

            return $this->render('car/create.html.twig',[
                'marques' => $marques,
                'categories' => $categories,
                'isAdmin' => true,
            ]);
        }else{
            $this->addFlash("Vous n'avez pas l'acces a cette page");
            return $this->redirectToRoute('home',);
        }
    }

    #[Route('/car_list', name: 'car.list')]
    public function carList(EntityManagerInterface $em,ImageRepository $imageRepository): Response
    {
        $voitures = $em->getRepository(Voiture::class)->findAllNotBooked();
        $images = $imageRepository->findAll();
        return $this->render('car/car_list.html.twig',[
            'voitures' => $voitures,
            'images' => $images,
        ]);
    }

    #[Route('/car_show/{id}', name: 'car.show')]
    public function carShow(EntityManagerInterface $em,$id):Response{
        $voiture= $em->getRepository(Voiture::class)->find($id);
        $image = $em->getRepository(Image::class)->findImageByCar($id);
        return $this->render('car/car.html.twig',[
            'voiture' => $voiture,
            'image' => $image,
        ]);
    }
    #[Route('/car_returned/{id}', name: 'car.return')]
    public function CarReturned(EntityManagerInterface $em,$id):Response{
        $user = $this->userRepository->findUserByEmailOrUsername($this->getUser()->getUserIdentifier());
        $idrole = $user->getProfile()->getId();
        if($role = $this->roleRepository->getRole($idrole, 'ADMIN')) {
            $reservation = $em->getRepository(Reservation::class)->getReservationById($id);
            $reservation->setStatut(1);
            $voiture = $reservation->getVoiture();
            $voiture->setStatutReservation(0);
            $voiture->setLieu($reservation->getLieuRetour());
            $em->persist($reservation);
            $em->persist($voiture);
            $em->flush();
            return $this->redirectToRoute('admin');
        }
        $this->addFlash("Vous n'avez pas l'acces a cette page");
        return $this->redirectToRoute('home',);
    }

    #[Route('/car_search/', name: 'car.search')]
    public function search(Request $request, EntityManagerInterface $em) {
        $marques = $em->getRepository(Marque::class)->findAll();
        $categories = $em->getRepository(Categorie::class)->findAll();

        if ($request->isMethod("GET")) {
            $query = $request->query->all();
            $voitures = $em->getRepository(Voiture::class)->findByFilter(
                $query['marques'] ?? [],
                $query['categories'] ?? [],
                $query['q'] ?? null
            );
            $images = $em->getRepository(Image::class)->findAll();

            return $this->render('car/car_list.html.twig', [
                'voitures' => $voitures,
                'images' => $images,
                'marques' => $marques,
                'categories' => $categories
            ]);
        }

        return $this->render('car/car_list.html.twig', [
            'voitures' => $em->getRepository(Voiture::class)->findAll(),
            'images' => $em->getRepository(Image::class)->findAll(),
            'marques' => $marques,
            'categories' => $categories
        ]);
    }

    #[Route('/car_edit/{id}', name: 'car.edit')]
    public function CarEdit(Request $request, EntityManagerInterface $em,$id,FileUploaderService $fileUploaderService){
        $user = $this->userRepository->findUserByEmailOrUsername($this->getUser()->getUserIdentifier());
        $idrole = $user->getProfile()->getId();
        if($role = $this->roleRepository->getRole($idrole, 'ADMIN')) {
            $voiture = $em->getRepository(Voiture::class)->find($id);
            $image = $em->getRepository(Image::class)->findImageByCar($id);
            $marques = $em->getRepository(Marque::class)->findAll();
            $categories = $em->getRepository(Categorie::class)->findAll();
            if($request->isMethod("POST")) {
                $voiture->setModele($request->get("modele"));
                $voiture->setCouleur($request->get("couleur"));
                $voiture->setLieu($request->get("lieu"));
                $voiture->setAttributs($request->get("attributs"));
                $voiture->setPrixParJour($request->get("prixParJour"));
                $voiture->setCreatedAt(new DateTimeImmutable('now'));
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
                $this->addFlash('success',"La voiture a ete modifier");
                return $this->redirectToRoute('papierVoiture',[
                    'id' => $voiture->getId()
                ]);
            }
            return $this->render('car/edit.html.twig',[
                'voiture' => $voiture,
                'image' => $image,
                'marques' => $marques,
                'categories' => $categories,
                'isAdmin'=> true,
            ]);
        }
        $this->addFlash("Vous n'avez pas l'acces a cette page");
        return $this->redirectToRoute('home',);
    }


}