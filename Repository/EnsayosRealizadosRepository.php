<?php

namespace App\Repository;

use App\Entity\EnsayosRealizados;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EnsayosRealizados|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnsayosRealizados|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnsayosRealizados[]    findAll()
 * @method EnsayosRealizados[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnsayosRealizadosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EnsayosRealizados::class);
    }

    // /**
    //  * @return EnsayosRealizados[] Returns an array of EnsayosRealizados objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EnsayosRealizados
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
