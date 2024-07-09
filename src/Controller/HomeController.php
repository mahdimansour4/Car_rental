<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController{

    #[Route('/', name: 'home')]
    public function index(Request $request,UserPasswordHasherInterface $hasher,
    EntityManagerInterface $em,
    ProfileRepository $profileRepository,
    RoleRepository $roleRepository):Response{
        return $this->render('home/index.html.twig');
    }

    #[Route('/about', name: 'about')]
    public function about(Request $request): Response{
        return $this->render('home/about.html.twig', );
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request): Response{
        return $this->render('home/contact.html.twig', );
    }
}
