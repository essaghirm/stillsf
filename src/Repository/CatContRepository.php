<?php

namespace App\Repository;

use App\Entity\CatCont;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CatCont|null find($id, $lockMode = null, $lockVersion = null)
 * @method CatCont|null findOneBy(array $criteria, array $orderBy = null)
 * @method CatCont[]    findAll()
 * @method CatCont[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatContRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CatCont::class);
    }

//    /**
//     * @return CatCont[] Returns an array of CatCont objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CatCont
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
