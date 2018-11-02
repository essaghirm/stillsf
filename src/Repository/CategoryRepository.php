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

    public function insertCategory($parent_id, $title, $old_id = null){

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT c.rgt, c.lvl, c.id
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
        $category->setLvl($parent['lvl']+1);
        // $category->setOldId($old_id);
        $category->setParent($entityManager->getRepository(Category::class)->find($parent_id));

        // dump($category);
        // die();


        $entityManager->persist($category);
        $entityManager->flush();
        return $category;
    }

    public function updateAfterRemoveCategory($lft)
    {
        $entityManager = $this->getEntityManager();

        $updateLeft = $entityManager->createQuery(
            'UPDATE App\Entity\Category c
            SET c.lft = c.lft - 2
            WHERE c.lft >= :lft'
        )->setParameter('lft', $lft)
        ->execute();

        $updateRight = $entityManager->createQuery(
            'UPDATE App\Entity\Category c
            SET c.rgt = c.rgt - 2
            WHERE c.rgt >= :lft'
        )->setParameter('lft', $lft)
        ->execute();

        return true;
    }

    /**
    * @return Category[] Returns an array of Category objects
    */
    public function getChilds($id){

        $parente = $query = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Category c
            WHERE c.id = $id"
        )->getOneOrNullResult();

        $right = $parente->getRgt();
        $left = $parente->getLft();

        $categories = $query = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Category c
            WHERE c.lft < $left and c.rgt > $right ORDER BY c.lft ASC"
        )->getResult();

        dump($categories);
        die();  
    }

    public function getParentDetails($id){

        $parent = $query = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Category c
            WHERE c.id = $id"
        )->getOneOrNullResult();

        $right = $parent->getRgt();
        $left = $parent->getLft();

        $categories = $this->getEntityManager()->createQueryBuilder()->select('cat')
                        ->from('App\Entity\Category', 'cat')
                        ->where('cat.lft > :lft AND cat.rgt < :rgt AND cat.lvl = 5')
                        ->setParameter('lft', $left)
                        ->setParameter('rgt', $right)
                        ->getQuery()
                        ->getResult();

        $contacts = $this->getEntityManager()->createQueryBuilder()->select('count(c.id)')
                        ->from('App\Entity\Contact', 'c')
                        ->where('c.category IN (:ids)')
                        ->orWhere('c.category = :parent')
                        ->setParameter('ids', $categories)
                        ->setParameter('parent', $parent)
                        ->getQuery()->getSingleScalarResult();
        
        return array(
            'contacts' => (int) $contacts,
            'categories' => sizeof($categories)
        );
        dump(sizeof($categories), $contacts);
        die();  
    }
}
