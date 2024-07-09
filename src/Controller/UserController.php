<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController{

    #[Route('/user/{id}/edit', name: 'user.edit', methods: ['GET', 'POST'])]
    public function editUser(Request $request,int $id,
                             UserRepository $repository,
                             EntityManagerInterface $em):Response{
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $repository->find($id);
        if($request->getMethod() == "POST"){
            $user->setUsername($request->get('username'));
            $user->setEmail($request->get('email'));
            $user->setTelephone($request->get('telephone'));
            $user->setCIN($request->get('cin'));
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user.show', ['id' => $id]);
        }
        return $this->render('user/editUser.html.twig',[
            'user' => $user
        ]);
    }

    #[Route('/user/{id}', name: 'user.show', methods: ['GET', 'POST'])]
    public function UserProfile(Request $request,int $id,
                                UserRepository $repository):Response{
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $repository->find($id);
        return $this->render('user/profile.html.twig',
        ['user' => $user]
        );
    }

//    #[Route('/user/{id}/delete', name: 'user.delete', methods: ['POST','GET'])]
//    public function deleteUser(Request $request,int $id,
//                               EntityManagerInterface $em,
//                               UserRepository $repository):Response{
//        $this->denyAccessUnlessGranted('ROLE_USER');
//        $user = $repository->find($id);
//        $em->remove($user);
//        $em->flush();
//        $this->addFlash('success',"Votre Compte a ete supprimee");
//        $request->getSession()->invalidate();
//        return $this->redirectToRoute('app_logout');
//    }

}