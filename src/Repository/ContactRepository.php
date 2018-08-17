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

        return $query = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM App\Entity\Contact c
            WHERE c.id IN $ids"
        )->getResult();
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
            WHERE c.lft < $left and c.rgt > $right ORDER BY c.lft ASC"
        )->getResult();

        dump($categories);
        die();
        
    }
}
