<?php

namespace App\Repository;

use App\Entity\SolucionesReactivosCantidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SolucionesReactivosCantidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method SolucionesReactivosCantidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method SolucionesReactivosCantidad[]    findAll()
 * @method SolucionesReactivosCantidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SolucionesReactivosCantidadRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SolucionesReactivosCantidad::class);
    }

    // /**
    //  * @return SolucionesReactivosCantidad[] Returns an array of SolucionesReactivosCantidad objects
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
    public function findOneBySomeField($value): ?SolucionesReactivos
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
