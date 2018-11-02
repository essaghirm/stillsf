<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Relation;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/diagnostic")
 */
class DiagnosticController extends Controller
{
    /**
     * @Route("/contact_without_company", name="contact_without_company", methods="GET")
     */
    public function contact_without_company(){
        $em = $this->getDoctrine()->getManager();
        $contactRepo = $this->getDoctrine()->getRepository(Contact::class);

        $relations = $contactRepo->getRelations11();

        $contact_with_company = [];
        foreach ($relations as $r) {
            if($r->getFriend()->getType() == 'company'){
                array_push($contact_with_company, $r->getContact());
            }else{
                array_push($contact_with_company, $r->getFriend());
            }
        }


        $contact_without_company = $contactRepo->createQueryBuilder('c')
                                    ->where("c.id NOT IN (:contact_with_company) AND c.type LIKE 'contact'")
                                    ->setParameter('contact_with_company', $contact_with_company)
                                    ->getQuery()->getResult();

        return $this->toJson($contact_without_company, array('myFriends', 'friendsWithMe', 'infos', 'created', 'category'));
    }

    /**
     * @Route("/contacts_double_name", name="contacts_double_name", methods="GET")
     */
    public function contacts_double_name(){
        $em = $this->getDoctrine()->getManager();
        
        $RAW_QUERY = "SELECT c.id, c.lname, c.fname, COUNT(*) as nbr FROM Contact c WHERE c.type LIKE 'contact' GROUP BY c.lname, c.fname HAVING COUNT(*) > 1;";
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
        $contacts = $statement->fetchAll();
        return $this->toJson($contacts, array('myFriends', 'friendsWithMe', 'infos', 'created', 'category'));
    }

    /**
     * @Route("/company_double_name", name="company_double_name", methods="GET")
     */
    public function company_double_name(){
        $em = $this->getDoctrine()->getManager();
        
        $RAW_QUERY = "SELECT c.id, c.lname, COUNT(*) as nbr FROM Contact c WHERE c.type LIKE 'company' GROUP BY c.lname HAVING COUNT(*) > 1;";
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
        $contacts = $statement->fetchAll();
        return $this->toJson($contacts, array('myFriends', 'friendsWithMe', 'infos', 'created', 'category'));
    }

    //

    /**
     * @Route("/contact_double_email", name="contact_double_email", methods="GET")
     */
    public function contact_double_email(){
        $em = $this->getDoctrine()->getManager();
        
        $RAW_QUERY = "SELECT i.*
                        FROM info i
                        JOIN (SELECT value, COUNT(*)
                        FROM info 
                        GROUP BY value
                        HAVING count(*) > 1 ) v
                        ON i.value = v.value
                        WHERE i.`type` LIKE 'Email'
                        ORDER BY i.value;";
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
        $infos = $statement->fetchAll();
        return $this->toJson($infos, array('myFriends', 'friendsWithMe', 'infos', 'created', 'category'));
    }

    /**
     * @Route("/contact_double_phone", name="contact_double_phone", methods="GET")
     */
    public function contact_double_phone(){
        $em = $this->getDoctrine()->getManager();
        
        $RAW_QUERY = "SELECT i.*
                        FROM info i
                        JOIN (SELECT value, COUNT(*)
                        FROM info 
                        GROUP BY value
                        HAVING count(*) > 1 ) v
                        ON i.value = v.value
                        WHERE i.`type` LIKE 'Phone'
                        ORDER BY i.value;";
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
        $infos = $statement->fetchAll();
        return $this->toJson($infos, array('myFriends', 'friendsWithMe', 'infos', 'created', 'category'));
    }

    /**
     * @Route("/contact_category_incoherent_with_their_companies", name="contact_category_incoherent_with_their_companies", methods="GET")
     */
    public function contact_category_incoherent_with_their_companies(){
        $em = $this->getDoctrine()->getManager();
        $contactRepo = $this->getDoctrine()->getRepository(Contact::class);
        $contacts = $contactRepo->getConComCat();
        return $this->toJson($contacts, array('myFriends', 'friendsWithMe', 'infos', 'created', 'category'));
    }


    public function toJson($objects, $ignoredAttributes = null){
        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes($ignoredAttributes);

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($objects, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}