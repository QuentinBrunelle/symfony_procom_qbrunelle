<?php

namespace App\Repository;

use App\Entity\Metier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Metier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Metier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Metier[]    findAll()
 * @method Metier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetierRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Metier::class);
    }
}
