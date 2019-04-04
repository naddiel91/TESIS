<?php

namespace App\Repository;

use App\Entity\Reactivos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Reactivos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reactivos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reactivos[]    findAll()
 * @method Reactivos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReactivosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Reactivos::class);
    }

    // /**
    //  * @return Reactivos[] Returns an array of Reactivos objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reactivos
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
