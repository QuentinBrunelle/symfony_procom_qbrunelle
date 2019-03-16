<?php

namespace App\Repository;

use App\Entity\TempsProductionEmployeProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TempsProductionEmployeProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method TempsProductionEmployeProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method TempsProductionEmployeProjet[]    findAll()
 * @method TempsProductionEmployeProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TempsProductionEmployeProjetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TempsProductionEmployeProjet::class);
    }

    public function findTopEmploye($id){
        return $this
            ->createQueryBuilder('t')
            ->addSelect('SUM(t.duree) as somme')
            ->andWhere('t.employe = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function findCoutTotalProjet($id){
        return $this
            ->createQueryBuilder('t')
            ->addSelect('SUM(t.coutTotal) as cout')
            ->andWhere('t.projet = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }


    public function findFourLastProducts(){
        return $this
            ->createQueryBuilder('p')
            ->addSelect('i')
            ->leftJoin('p.image', 'i')
            ->orderBy('p.created_at', 'DESC')
            ->setMaxResults('4')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return TempsProductionEmployeProjet[] Returns an array of TempsProductionEmployeProjet objects
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
    public function findOneBySomeField($value): ?TempsProductionEmployeProjet
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
