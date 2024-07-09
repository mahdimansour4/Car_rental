<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function getRole(int $idprofile,string $code){
        return $this->createQueryBuilder('r')
            ->innerJoin('r.profile','p')
            ->innerJoin('p.roles','rt')
            ->where('rt.code = :code')
            ->andWhere('p.id = :profile')
            ->setParameter('code',$code)
            ->setParameter('profile',$idprofile)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getRoleByCode(string $code, int $profileId): ?Role
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.profile', 'p')
            ->where('r.code = :code')
            ->andWhere('p.id = :profile')
            ->setParameter('code', $code)
            ->setParameter('profile', $profileId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getUsersByRole(string $coderole): array
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.profile', 'p')
            ->innerJoin('p.roles', 'rt')
            ->innerJoin('p.users', 'u')
            ->select('u.id', 'u.username', 'u.email', 'u.cin', 'u.telephone','u.createdAt', 'r.id AS roleId', 'r.code', 'r.name')
            ->where('rt.code = :coderole')
            ->setParameter('coderole', $coderole)
            ->getQuery()
            ->getArrayResult();
    }

//    /**
//     * @return Role[] Returns an array of Role objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Role
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
