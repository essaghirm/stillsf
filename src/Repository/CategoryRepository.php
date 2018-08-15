<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function insertCategory($parent_id, $title){

        // $parent = $this->createQueryBuilder('c')
        //     ->andWhere('c.id = :val')
        //     ->setParameter('val', $parent_id)
        //     ->getQuery()
        //     ->getOneOrNullResult();

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT c.rgt
            FROM App\Entity\Category c
            WHERE c.id = :id'
        )->setParameter('id', $parent_id)
        ->setMaxResults(1);
        $parent = $query->getOneOrNullResult();

        $updateRight = $entityManager->createQuery(
            'UPDATE App\Entity\Category c
            SET c.rgt = c.rgt + 2
            WHERE c.rgt >= :rgt'
        )->setParameter('rgt', $parent['rgt'])
        ->execute();

        $updateLeft = $entityManager->createQuery(
            'UPDATE App\Entity\Category c
            SET c.lft = c.lft + 2
            WHERE c.lft >= :lft'
        )->setParameter('lft', $parent['rgt'])
        ->execute();

        $category = new Category;
        $category->setTitle($title);
        $category->setLft($parent['rgt']);
        $category->setRgt($parent['rgt']+1);

        $entityManager->persist($category);
        $entityManager->flush();
        return $category;
    }

    public function deleteCategory($id)
    {
        # code...
    }

//    /**
//     * @return Category[] Returns an array of Category objects
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
    public function findOneBySomeField($value): ?Category
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
