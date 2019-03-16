<?php

namespace App\Repository;

use App\Entity\TempsDeProduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TempsDeProduction|null find($id, $lockMode = null, $lockVersion = null)
 * @method TempsDeProduction|null findOneBy(array $criteria, array $orderBy = null)
 * @method TempsDeProduction[]    findAll()
 * @method TempsDeProduction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TempsDeProductionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TempsDeProduction::class);
    }

    // /**
    //  * @return TempsDeProduction[] Returns an array of TempsDeProduction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TempsDeProduction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
