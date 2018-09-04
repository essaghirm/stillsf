<?php

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\Relation;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getRelations($value){
        $qb = $this->getEntityManager()->createQueryBuilder();

        $friends = $qb->select('r')
            ->from('App\Entity\Relation', 'r')
            ->innerJoin('App\Entity\Contact', 'c')
            ->andWhere('r.contact = :val OR r.friend = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
        
        if(empty($friends)){
            return null;
        }

        $ids = [];

        foreach ($friends as $k => $f) {
            if($f->getFriend()->getId() == $value){
                $ids[] = $f->getContact()->getId(); 
            }else{
               $ids[] = $f->getFriend()->getId();   
            }       
        }
        $ids = '('.implode(',', $ids).')';

        // echo $ids;
        // die();

        $contacts = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Contact c
            WHERE c.id IN $ids and c.type like 'contact'"
        )->getResult();

        $companies = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Contact c
            WHERE c.id IN $ids and c.type like 'company'"
        )->getResult();

        return array('contacts' => $contacts, 'companies' => $companies);
    }

    /**
    * @return Category[] Returns an array of Category objects
    */
    public function getCategories($id){

        $child = $query = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Category c
            WHERE c.id = $id"
        )->getOneOrNullResult();

        $right = $child->getRgt();
        $left = $child->getLft();

        $categories = $query = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Category c
            WHERE c.lft <= $left and c.rgt >= $right ORDER BY c.lft ASC"
        )->getResult();
        return $categories;

        // dump($categories);
        // die();
        
    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getContactsById($value, $criteria){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $category = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Category c
            WHERE c.id = ".$criteria['category_id']
        )->getOneOrNullResult();

        if($category != null){
            $right = $category->getRgt();
            $left = $category->getLft();
        }

        dump($left, $right);

        $result = $qb->select('c')
        ->from('App\Entity\Contact', 'c')
        ->innerJoin('App\Entity\Category', 'cat')
        ->andWhere('c.id like :value AND cat.lft >= :lft AND cat.rgt <= :rgt')
        // ->andWhere('c.type LIKE :type')
        // ->andWhere('cat.lft >= :lft AND cat.rgt <= :rgt')
        ->orderBy('c.id', 'ASC')
        ->setParameter('lft', $left)
        ->setParameter('rgt', $right)
        ->setParameter('value', $value.'%')
        // ->setParameter('type', $criteria['type'])
        ->getQuery()
        ->getResult();

        return $result;

        // dump($result);
        // die();


    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getContactsByPhone($value, $criteria){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $category = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Category c
            WHERE c.id = ".$criteria['category_id']
        )->getOneOrNullResult();

        if($category != null){
            $right = $category->getRgt();
            $left = $category->getLft();
        }

        $result = $qb->select('c')
        ->from('App\Entity\Contact', 'c')
        ->innerJoin('App\Entity\Category', 'cat')
        ->andWhere('c.id like :value')
        // ->andWhere('c.type LIKE :type')
        ->andWhere('cat.lft >= :lft AND cat.rgt <= :rgt')
        ->orderBy('c.id', 'ASC')
        ->setParameter('lft', $left)
        ->setParameter('rgt', $right)
        ->setParameter('value', $value.'%')
        // ->setParameter('type', $criteria['type'])
        ->getQuery()
        ->getResult();

        return $result;

        // dump($result);
        // die();


    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getContactsByName($value, $criteria){
        $qb = $this->getEntityManager()->createQueryBuilder();

        // $contactRepo = $this->getRepository(Contact::class);


        $category = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Category c
            WHERE c.id = ".$criteria['category_id']
        )->getOneOrNullResult();

        if($category != null){
            $right = $category->getRgt();
            $left = $category->getLft();
        }

        // dump($left, $right);

        

        // $contacts = $qb->select('c')
        //     ->from('App\Entity\Contact', 'c')
        //     ->innerJoin('App\Entity\Category', 'cat')
        //     ->andWhere('r.contact = :val OR r.friend = :val')
        //     ->setParameter('val', $value)
        //     ->orderBy('c.id', 'ASC')
        //     ->getQuery()
        //     ->getResult();

            $name = explode(' ', $value);

            if(sizeof($name) > 1){
                $result = $qb->select('c')
                ->from('App\Entity\Contact', 'c')
                ->innerJoin('App\Entity\Category', 'cat')
                ->andWhere('c.fname LIKE :value OR c.lname like :value')
                ->orWhere('c.lname like :n1 AND c.fname like :n2')
                ->orWhere('c.fname like :n1 AND c.lname like :n2')
                // ->andWhere('c.type LIKE :type')
                ->andWhere('cat.lft >= :lft AND cat.rgt <= :rgt')
                ->orderBy('c.id', 'ASC')
                ->setParameter('lft', $left)
                ->setParameter('rgt', $right)
                ->setParameter('value', $value.'%')
                ->setParameter('n1', $name[0].'%')
                ->setParameter('n2', $name[1].'%')
                // ->setParameter('type', $criteria['type'])
                ->getQuery()
                ->getResult();
            }else{
                $result = $qb->select('c')
                ->from('App\Entity\Contact', 'c')
                ->innerJoin('App\Entity\Category', 'cat')
                ->andWhere('c.fname LIKE :value')
                ->orWhere('c.lname like :value')
                ->orWhere('c.fname like :value')
                // ->andWhere('c.type LIKE :type')
                ->andWhere('cat.lft >= :lft AND cat.rgt <= :rgt')
                ->orderBy('c.id', 'ASC')
                ->setParameter('lft', $left)
                ->setParameter('rgt', $right)
                ->setParameter('value', $value.'%')
                // ->setParameter('type', $criteria['type'])
                ->getQuery()
                ->getResult();
            }

            return $result;
    }
}
