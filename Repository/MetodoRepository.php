<?php

namespace App\Repository;

use App\Entity\Metodo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Metodo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Metodo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Metodo[]    findAll()
 * @method Metodo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetodoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Metodo::class);
    }

    // /**
    //  * @return Metodo[] Returns an array of Metodo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Metodo
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
