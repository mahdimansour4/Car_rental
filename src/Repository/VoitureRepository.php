<?php

namespace App\Repository;

use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voiture>
 */
class VoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    public function findAllNotBooked(){
        return $this->createQueryBuilder('v')
            ->where('v.statutReservation = :statusReservation')
            ->setParameter('statusReservation', 0)
            ->getQuery()
            ->getResult();
    }


    public function findByQuery($query) {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.marque', 'm')
            ->leftJoin('v.categorie', 'c')
            ->where('v.modele LIKE :query')
            ->orWhere('m.nom LIKE :query')
            ->orWhere('c.nom LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByFilter(array $marques, array $categories, string $nom)
    {
        $qb = $this->createQueryBuilder('v');

        if (!empty($categories)) {
            $qb->andWhere('v.categorie IN (:categories)')
                ->setParameter('categories', $categories);
        }

        if (!empty($marques)) {
            $qb->andWhere('v.marque IN (:marques)')
                ->setParameter('marques', $marques);
        }

        if (!empty($nom)) {
            $qb->andWhere('v.modele LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%');
        }

        return $qb->getQuery()->getResult();
    }



//    /**
//     * @return Voiture[] Returns an array of Voiture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Voiture
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
