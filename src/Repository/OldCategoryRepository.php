<?php

namespace App\Repository;

use App\Entity\OldCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OldCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method OldCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method OldCategory[]    findAll()
 * @method OldCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OldCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OldCategory::class);
    }

//    /**
//     * @return OldCategory[] Returns an array of OldCategory objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OldCategory
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
