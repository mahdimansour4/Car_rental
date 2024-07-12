<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Voiture;
use App\Repository\ImageRepository;
use App\Repository\ReservationRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReservationController extends  AbstractController{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository,
    ) {
    }

    #[Route('/car/{id}/reservation_create', name: 'reservation.create')]
        public function createReservation(Request $request,EntityManagerInterface $em,$id,ValidatorInterface $validator): Response{
        $user = $this->userRepository->findUserByEmailOrUsername($this->getUser()->getUserIdentifier());
        $iduser = $user->getProfile()->getId();
        if($role = $this->roleRepository->getRole($iduser, 'USER')){
            if($request->getMethod() == 'POST'){
                $voiture = $em->getRepository(Voiture::class)->find($id);
                $reservation = new Reservation();
                $reservation->setUser($user);
                $reservation->setVoiture($voiture);
                $reservation->setCreatedAt(new \DateTimeImmutable('now'));
                $reservation->setDateDebut(new \DateTime($request->get('dateDebut')));
                $dateDebut = new \DateTime($request->get('dateDebut'));
                $reservation->setDateFin(new \DateTime($request->get('dateFin')));
                $dateFin = new \DateTime($request->get('dateFin'));
                $jours = $dateDebut->diff($dateFin);
                $jours = $jours->d;
                $reservation->setLieuRecup($request->get('lieuRecup'));
                $reservation->setLieuRetour($request->get('lieuRetour'));
                $reservation->setPrixTotal($voiture->getPrixParJour() * ((float)$jours) );
                $em->getRepository(Voiture::class)->find($id)->setStatutReservation('true');
                $errors = $validator->validate($reservation);
                if(count($errors) > 0){
                    $this->addFlash('error','Vous avez commis une erreur dans votre reservation');
                    return $this->redirectToRoute('reservation.create');
                }
                $em->persist($reservation);
                $em->flush();
                $this->addFlash('success',"Vous avez fait une reservation");
                return $this->redirectToRoute('home');
            }
            return $this->render('reservation/create.html.twig', [
                'id' => $id
            ]);
        }
        return $this->redirectToRoute('home');
    }

    #[Route('/my_reservations', name: 'my.reservations')]
    public function indexReservation(Request $request,EntityManagerInterface $em,ReservationRepository $reservationRepository,UserRepository $userRepository):Response{
        $reservations = $reservationRepository->findBy(['user'=>$userRepository->findUserByEmailOrUsername($this->getUser()->getUserIdentifier())]);
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/my_reservation/{id}/delete', name: 'my.reservation.delete')]
    public function deleteReservation(Request $request,EntityManagerInterface $em,ReservationRepository $reservationRepository,UserRepository $userRepository,$id):Response{
        $reservation = $reservationRepository->find($id);
        $reservation->getVoiture()->setStatutReservation(0);
        $em->remove($reservation);
        $em->flush();
        return $this->redirectToRoute('my.reservations');
    }
}