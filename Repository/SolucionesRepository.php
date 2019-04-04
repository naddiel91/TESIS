<?php

namespace App\Repository;

use App\Entity\Soluciones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Soluciones|null find($id, $lockMode = null, $lockVersion = null)
 * @method Soluciones|null findOneBy(array $criteria, array $orderBy = null)
 * @method Soluciones[]    findAll()
 * @method Soluciones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SolucionesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Soluciones::class);
    }

    // /**
    //  * @return Soluciones[] Returns an array of Soluciones objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Soluciones
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
