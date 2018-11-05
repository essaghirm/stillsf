<?php

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\Relation;
use App\Entity\Info;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\Expr\Join;

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

        // dump($friends);die;

        $rel_array = ['contacts' => [], 'companies' => []];

        foreach ($friends as $k => $f) {
            if($f->getFriend()->getId() == $value){
                if($f->getContact()->getType() == 'contact'){
                    array_push(
                        $rel_array['contacts'],array('contact' => $f->getContact(), 'occupation' => $f->getOccupation()));
                }else{
                    array_push(
                        $rel_array['companies'],array('contact' => $f->getContact(), 'occupation' => $f->getOccupation()));
                }
            }else{
               if($f->getFriend()->getType() == 'contact'){
                    array_push($rel_array['contacts'], array('contact' => $f->getFriend(), 'occupation' => $f->getOccupation()));
                }else{
                    array_push($rel_array['companies'], array('contact' => $f->getFriend(), 'occupation' => $f->getOccupation()));
                }
            }       
        }      

        // $return = array('contacts' => $contacts, 'companies' => $companies);
        return $rel_array;
    }

    public function deleteRelationForContact($id){

        $qb = $this->getEntityManager()->createQuery('delete from App\Entity\Relation r where r.contact = :id or r.friend = :id')
                ->setParameter('id', $id);

        return $qb->execute();
    }

    public function deleteInfosForContact($id){

        $qb = $this->getEntityManager()->createQuery('delete from App\Entity\Info i where i.contact = :id')
                ->setParameter('id', $id);

        return $qb->execute();
    }

    public function deleteRelationWithContact($contact, $friend){

        $qb = $this->getEntityManager()->createQuery('delete from App\Entity\Relation r where (r.contact = :contact AND r.friend = :friend) OR (r.contact = :friend AND r.friend = :contact)')
                ->setParameter('contact', $contact)
                ->setParameter('friend', $friend);

        return $qb->execute();
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
    public function getContactsById($id, $criteria, $offset, $limit){
        $qb = $this->getEntityManager()->createQueryBuilder();        

        if(isset($criteria['category_id']) && is_numeric($criteria['category_id'])){
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

            $categories = $this->getEntityManager()->createQueryBuilder()->select('cat')
                        ->from('App\Entity\Category', 'cat')
                        ->where('cat.lft >= :lft AND cat.rgt <= :rgt AND cat.lvl = 5')
                        ->setParameter('lft', $left)
                        ->setParameter('rgt', $right)
                        ->getQuery()
                        ->getResult();
        }else{
            $categories = null;
        }

        $result = $this->getEntityManager()->createQueryBuilder()->select('c')
                    ->from('App\Entity\Contact', 'c')
                    ->where('c.id like :id')
                    ->setParameter('id', $id.'%');
        if($categories != null){
            $result->andWhere('c.category IN (:ids)')
                    ->setParameter('ids', $categories);
        }
        if($criteria['type'] != null){
            $result->andWhere('c.type like :type')
                    ->setParameter('type', $criteria['type']);
        }
        $array['total'] = sizeof($result->getQuery()->getResult());
        $array['contacts'] = $result->setFirstResult( $offset )
        ->setMaxResults( $limit )
        ->getQuery()->getResult();

        return $array;
    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getContactsByPhone($value, $criteria, $offset, $limit){

        $number = sprintf("%s %s %s",
	              substr($value, 0, 3),
	              substr($value, 3, 3),
	              substr($value, 6, 3));
        $number = rtrim($number);

        if(isset($criteria['category_id']) && is_numeric($criteria['category_id'])){
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

            $categories = $this->getEntityManager()->createQueryBuilder()->select('cat')
                        ->from('App\Entity\Category', 'cat')
                        ->where('cat.lft >= :lft AND cat.rgt <= :rgt AND cat.lvl = 5')
                        ->setParameter('lft', $left)
                        ->setParameter('rgt', $right)
                        ->getQuery()
                        ->getResult();
        }else{
            $categories = null;
        }
    
        

        $infos = $this->getEntityManager()->createQueryBuilder()->select('i')
                    ->from('App\Entity\Info', 'i')
                    ->where('i.value like :number')
                    ->andWhere('i.type like :type')
                    ->setParameter('number', '%) '.$number.'%')
                    ->setParameter('type', 'Phone')
                    ->getQuery()
                    ->getResult();

                    $contacts = [];

        foreach ($infos as $i) {
            array_push($contacts, $i->getContact());
        }

        $result = $this->getEntityManager()->createQueryBuilder()->select('c')
                    ->from('App\Entity\Contact', 'c')
                    ->where('c.id IN (:contacts)')
                    ->setParameter('contacts', $contacts);
        if($categories != null){
            $result->andWhere('c.category IN (:ids)')
                    ->setParameter('ids', $categories);
        }
        if($criteria['type'] != null){
            $result->andWhere('c.type like :type')
                    ->setParameter('type', $criteria['type']);
        }
        $array['total'] = sizeof($result->getQuery()->getResult());
        $array['contacts'] = $result->setFirstResult( $offset )
        ->setMaxResults( $limit )
        ->getQuery()->getResult();

        return $array;
    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getContactsByName($value, $criteria, $offset, $limit){
        $qb = $this->getEntityManager()->createQueryBuilder();

        // $contactRepo = $this->getRepository(Contact::class);


        if(isset($criteria['category_id']) && is_numeric($criteria['category_id'])){
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

            $categories = $this->getEntityManager()->createQueryBuilder()->select('cat')
                        ->from('App\Entity\Category', 'cat')
                        ->where('cat.lft >= :lft AND cat.rgt <= :rgt AND cat.lvl = 5')
                        ->setParameter('lft', $left)
                        ->setParameter('rgt', $right)
                        ->getQuery()
                        ->getResult();
        }else{
            $categories = null;
        }


        $name = explode(' ', $value);

        if(sizeof($name) > 1){
            $result = $this->getEntityManager()->createQueryBuilder()->select('c')
            ->from('App\Entity\Contact', 'c')
            ->andWhere('c.fname LIKE :value OR c.lname like :value')
            ->orWhere('c.lname like :n1 AND c.fname like :n2')
            ->orWhere('c.fname like :n1 AND c.lname like :n2')
            // ->andWhere('c.type LIKE :type')
            ->orderBy('c.lname', 'ASC')
            ->setParameter('value', '%'.$value.'%')
            ->setParameter('n1', '%'.$name[0].'%')
            ->setParameter('n2', '%'.$name[1].'%');
            if($categories != null){
                $result->andWhere('c.category IN (:ids)')
                        ->setParameter('ids', $categories);
            }
            if($criteria['type'] != null){
                $result->andWhere('c.type like :type')
                        ->setParameter('type', $criteria['type']);
            }
            $array['total'] = sizeof($result->getQuery()->getResult());
            $array['contacts'] = $result->setFirstResult( $offset )
            ->setMaxResults( $limit )
            ->getQuery()->getResult();

            return $array;
        }else{
            $result = $qb->select('c')
            ->from('App\Entity\Contact', 'c')
            ->andWhere('c.fname LIKE :value')
            ->orWhere('c.lname like :value')
            ->orWhere('c.fname like :value')
            ->orderBy('c.id', 'ASC')
            ->setParameter('value', '%'.$value.'%');
            if($categories != null){
                $result->andWhere('c.category IN (:ids)')
                        ->setParameter('ids', $categories);
            }
            if($criteria['type'] != null){
                $result->andWhere('c.type like :type')
                        ->setParameter('type', $criteria['type']);
            }
            $array['total'] = sizeof($result->getQuery()->getResult());
            $array['contacts'] = $result->setFirstResult( $offset )
            ->setMaxResults( $limit )
            ->getQuery()->getResult();

            return $array;
        }

        

        return $result;
    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getContactsByFullText($value, $criteria, $offset, $limit){
        $em = $this->getEntityManager();

        // $contactRepo = $this->getRepository(Contact::class);


        if(isset($criteria['category_id']) && is_numeric($criteria['category_id'])){
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

            $categories = $this->getEntityManager()->createQueryBuilder()->select('cat')
                        ->from('App\Entity\Category', 'cat')
                        ->where('cat.lft >= :lft AND cat.rgt <= :rgt AND cat.lvl = 5')
                        ->setParameter('lft', $left)
                        ->setParameter('rgt', $right)
                        ->getQuery()
                        ->getResult();
        }else{
            $categories = null;
        }

        $infos = $em->createQueryBuilder()->select('i')
                    ->from('App\Entity\Info', 'i')
                    ->where('i.value like :value')
                    ->setParameter('value', '%'.$value.'%')
                    ->getQuery()
                    ->getResult();

        $contacts = [];

        foreach ($infos as $i) {
            array_push($contacts, $i->getContact());
        }
        
        $result = $em->createQueryBuilder()->select('c')
        ->from('App\Entity\Contact', 'c')
        ->andWhere('c.fname LIKE :value')
        ->orWhere('c.lname like :value')
        ->orWhere('c.fname like :value')
        ->orWhere('c.notes like :value')
        ->orWhere('c.city like :value')
        ->orWhere('c.web_site like :value')
        ->orWhere('c.id IN (:contacts)')
        ->orderBy('c.id', 'ASC')
        ->setParameter('value', $value.'%')
        ->setParameter('contacts', $contacts);
        if($categories != null){
            $result->andWhere('c.category IN (:ids)')
                    ->setParameter('ids', $categories);
        }
        if($criteria['type'] != null){
            $result->andWhere('c.type like :type')
                    ->setParameter('type', $criteria['type']);
        }

        $array['total'] = sizeof($result->getQuery()->getResult());
        $array['contacts'] = $result->setFirstResult( $offset )
        ->setMaxResults( $limit )
        ->getQuery()->getResult();

        return $array;
    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getContactsByTriangle($value, $criteria, $offset, $limit){
        $qb = $this->getEntityManager()->createQueryBuilder();

        // $contactRepo = $this->getRepository(Contact::class);


        $name = explode(' ', $value);

        if(sizeof($name) > 1){
            $result = $this->getEntityManager()->createQueryBuilder()->select('c')
            ->from('App\Entity\Contact', 'c')
            ->andWhere('c.fname LIKE :value OR c.lname like :value')
            ->orWhere('c.lname like :n1 AND c.fname like :n2')
            ->orWhere('c.fname like :n1 AND c.lname like :n2')
            ->andWhere('c.type LIKE :type')
            ->orderBy('c.lname', 'ASC')
            ->setParameter('value', $value.'%')
            ->setParameter('n1', $name[0].'%')
            ->setParameter('n2', $name[1].'%')
            ->setParameter('type', 'contact')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit )
            ->getQuery()->getResult();
        }else{
            $result = $qb->select('c')
            ->from('App\Entity\Contact', 'c')
            ->andWhere('c.fname LIKE :value')
            ->orWhere('c.lname like :value')
            ->orWhere('c.fname like :value')
            ->andWhere('c.type LIKE :type')
            ->orderBy('c.id', 'ASC')
            ->setParameter('value', $value.'%')
            ->setParameter('type', 'contact')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit )
            ->getQuery()->getResult();
        }

        $array = [];

        if(sizeof($result) > 0){
            foreach ($result as $key => $c) {
                
                $relations = $this->getRelations($c->getId());
                if(sizeof($relations['contacts']) > 0 || sizeof($relations['companies']) > 0){
                    $array[$key]['contact'] = $c;
                    // $array[$key]['relations'] = $relations;
                    foreach ($relations['contacts'] as $k => $r) {
                        $array[$key]['relations'][$k] = $r;
                    }
                    foreach ($relations['companies'] as $k => $r) {
                        $array[$key]['relations'][ sizeof($relations['contacts']) + $k] = $r;
                    }
                }else{
                    continue;
                }
            }

        }else{
            return null;
        }

        // dump($array);die;

        return $array;
    }


    public function getRelations11(){
        $contacts = $this->findBy(array('type' => 'contact'));
        $companies = $this->findBy(array('type' => 'company'));

        return $this->getEntityManager()->createQueryBuilder()->select('r')
                    ->from('App\Entity\Relation', 'r')
                    ->where("r.contact IN (:companies) AND r.friend IN (:contacts)")
                    ->orWhere("r.contact IN (:contacts) AND r.friend IN (:companies)")
                    ->setParameter('contacts', $contacts)
                    ->setParameter('companies', $companies)
                    ->getQuery()
                    ->getResult();
    }

    public function getConComCat()
    {
        $contacts = $this->findBy(array('type' => 'contact'));
        $array = [];
        foreach ($contacts as $c) {
            $ok = false;
            $ok = $this->getContactCompanies($c);
            if($ok == true)
                // dump($ok, $c->getId());
                array_push($array, $c);

        }

        return $array;

    }

    public function getContactCompanies($contact)
    {
        $em = $this->getEntityManager();

        $relations = $em->createQueryBuilder()->select('r')
            ->from('App\Entity\Relation', 'r')
            ->innerJoin('App\Entity\Contact', 'c')
            ->andWhere("r.contact = :val OR r.friend = :val")
            ->setParameter('val', $contact->getId())
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
            

        $companies_ids = [];

        foreach ($relations as $r) {
            if($r->getContact()->getType() == 'company'){
                array_push($companies_ids, $r->getContact());
            }else{
                array_push($companies_ids, $r->getFriend());
            }
        }

        $companies = $em->createQueryBuilder()->select('c')
            ->from('App\Entity\Contact', 'c')
            ->where('c.id IN (:companies_ids)')
            ->setParameter('companies_ids', $companies_ids)
            ->getQuery()
            ->getResult();
        // dump($contact->getCategory()->getId(), $companies);die;

        foreach ($companies as $com) {
            if($com->getCategory()->getId() == $contact->getCategory()->getId())
                return false;
        }
        return true;

        dump($companies);die;
    }
}
