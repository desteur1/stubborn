<?php

namespace App\Repository;

use App\Entity\Sweatshirt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sweatshirt>
 */
class SweatshirtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sweatshirt::class);
    }

    //    /**
    //     * @return Sweatshirt[] Returns an array of Sweatshirt objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sweatshirt
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByPriceRange(float $min, float $max) // méthode personnalisée pour trouver les produits dans une fourchette de prix
{
    return $this->createQueryBuilder('s')
        ->andWhere('s.price >= :min AND s.price < :max') // on utilise >= pour inclure le prix minimum et < pour exclure le prix maximum, afin d'éviter les chevauchements entre les plages de prix
        ->setParameter('min', $min)// on définit les paramètres de la requête pour éviter les injections SQL
        ->setParameter('max', $max)// on définit les paramètres de la requête pour éviter les injections SQL
        ->getQuery()
        ->getResult();
}
}
