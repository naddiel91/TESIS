<?php

namespace App\Repository;

use App\Entity\Analisis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Analisis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Analisis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Analisis[]    findAll()
 * @method Analisis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalisisRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Analisis::class);
    }

    // /**
    //  * @return Analisis[] Returns an array of Analisis objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Analisis
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
