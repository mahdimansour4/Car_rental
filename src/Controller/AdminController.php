<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\ProfileRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository,
        private readonly VoitureRepository $voitureRepository,
        private readonly ImageRepository $imageRepository,
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function admin(Request $request): Response
    {
        $user = $this->userRepository->findUserByEmailOrUsername($this->getUser()->getUserIdentifier());
        $id = $user->getProfile()->getId();

        if ($role = $this->roleRepository->getRole($id, 'ADMIN')) {
            $normalusers = $this->roleRepository->getUsersByRole('USER');
            $admins = $this->roleRepository->getUsersByRole('ADMIN');
            $voitures = $this->voitureRepository->findAll();
            $images = $this->imageRepository->findAll();

            return $this->render('admin/index.html.twig', [
                'users' => $normalusers,
                'admins' => $admins,
                'isAdmin' => true,
                'voitures' => $voitures,
                'images' => $images,
            ]);
        } else {
            return $this->redirectToRoute('home', [], Response::HTTP_BAD_REQUEST);
        }
    }
}