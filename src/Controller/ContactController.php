<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Category;
use App\Entity\Relation;
use App\Form\ContactType;
use App\Repository\ContactRepository;
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
 * @Route("/contact")
 */
class ContactController extends Controller
{

    public function test(ContactRepository $contactRepository): Response
    {
        return new Response('ok');
    }
    /**
     * @Route("/p/{p}", name="contact_index", methods="GET")
     */
    public function index(ContactRepository $contactRepository, $p): Response
    {
        $limit=40;
        $offset = ($p-1)*$limit;
        $contacts = $contactRepository->findBy(array(), array('id' => 'ASC'), $limit, $offset);

        foreach ($contacts as $c) {
            foreach ($c->getInfos() as $i) {
                switch ($i->getType()) {
                    case 'LandLine':
                        $c->setLandLine($i->getValue());
                        break;
                    case 'Mobile':
                        $c->setMobile($i->getValue());
                        break;
                    case 'Email':
                        $c->setEmail($i->getValue());
                        break;
                }
            }
        }

        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('myFriends', 'friendsWithMe', 'created', 'category'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($contacts, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/", name="contact_new", methods="POST")
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $contact = new Contact();
        $data = json_decode($request->getContent(), true);       
        
        $form = $this->createForm(ContactType::class, $contact);
        $form->submit($data);
        $contact->setCreated(new \DateTime());

        $contact->setCategory($this->getDoctrine()->getRepository(Category::class)->find($data['category']));

        $errors = $validator->validate($contact);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($contact);
        $em->flush();

        $response = $this->forward('App\Controller\ContactController::show', array(
            'id'  => $contact
        ));
        
        return $response;
    }

    /**
     * @Route("/{id}", name="contact_show", methods="GET")
     */
    public function show(Contact $contact): Response
    {
        $return = [];
        $return['contact'] = $contact;
        $return['relations'] = $this->getDoctrine()->getRepository(Contact::class)->getRelations($contact->getId());
        $return['categories'] = $this->getDoctrine()->getRepository(Contact::class)->getCategories($contact->getCategory()->getId());


        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('myFriends', 'friendsWithMe', 'contacts', 'category', 'children', 'parent'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $objec t->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($return, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}/details", name="contact_details", methods="GET")
     */
    public function details(Contact $contact): Response
    {
        $return = [];
        $return['relations'] = $this->getDoctrine()->getRepository(Contact::class)->getRelations($contact->getId());
        $return['categories'] = $this->getDoctrine()->getRepository(Contact::class)->getCategories($contact->getCategory()->getId());


        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('myFriends', 'friendsWithMe', 'contacts', 'category', 'children', 'parent'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $objec t->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($return, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}", name="contact_edit", methods="PUT")
     */
    public function edit(Request $request, Contact $contact): Response
    {        
        $data = json_decode($request->getContent(), true);      
        
        $contact->setFname($data['fname']);
        if($data['type'] == 'company'){
            $contact->setFname("");
        }
        $contact->setLname($data['lname']);
        $contact->setWebSite($data['web_site']);
        $contact->setNotes($data['notes']);
        $contact->setType($data['type']);
        $contact->setCity($data['city']);

        if($contact->getCategory()->getId() != $data['category'] && is_numeric($data['category']) && isset($data['category'])){
            $contact->setCategory($this->getDoctrine()->getRepository(Category::class)->find($data['category']));
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->forward('App\Controller\ContactController::show', array(
            'id'  => $contact->getId()
        ));
    }

    /**
     * @Route("/{id}", name="contact_delete", methods="DELETE")
     */
    public function delete(Request $request, Contact $contact): Response
    {

        $relations = $this->getDoctrine()->getRepository(Contact::class)->deleteRelationForContact($contact->getId());
        $infos = $this->getDoctrine()->getRepository(Contact::class)->deleteInfosForContact($contact->getId());
        // dump($relations);
        // die();

        $em = $this->getDoctrine()->getManager();
        
        $em->remove($contact);
        $em->flush();

        return new JsonResponse("ok");
    }

    /**
     * @Route("/relation/{contact}/{friend}", name="contact_delete_relation", methods="DELETE")
     */
    public function deleteRelationWithContact(Request $request, $contact, $friend): Response
    {

        $r = $this->getDoctrine()->getRepository(Contact::class)->deleteRelationWithContact($contact, $friend);
        // dump($r);
        // die();
        return $this->forward('App\Controller\ContactController::show', array(
            'id'  => $contact
        ));
    }

    /**
     * @Route("/search/{type}/{value}", name="relation_search", methods="GET")
     */
    public function searchRelations($type, $value): Response
    {
        $em = $this->getDoctrine()->getManager();
        $contactRepo = $this->getDoctrine()->getRepository(Contact::class);
        if(is_numeric($value)){
            $result = $contactRepo->createQueryBuilder('c')
            ->where('c.id LIKE :id')
            ->andWhere('c.type LIKE :type')
            ->orderBy('c.id', 'ASC')
            ->setParameter('id', $value.'%')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
        }else{
            $name = explode(' ', $value);
            // dump($name);
            // die();

            if(sizeof($name) > 1){
                $result = $contactRepo->createQueryBuilder('c')
                ->where('c.fname LIKE :value OR c.lname like :value')
                ->orWhere('c.lname like :n1 AND c.fname like :n2')
                ->orWhere('c.fname like :n1 AND c.lname like :n2')
                ->andWhere('c.type LIKE :type')
                ->orderBy('c.id', 'ASC')
                ->setParameter('value', $value.'%')
                ->setParameter('n1', $name[0].'%')
                ->setParameter('n2', $name[1].'%')
                ->setParameter('type', $type)
                ->getQuery()
                ->getResult();
            }else{
                $result = $contactRepo->createQueryBuilder('c')
                ->where('c.fname LIKE :value')
                ->orWhere('c.lname like :value')
                ->orWhere('c.fname like :value')
                ->andWhere('c.type LIKE :type')
                ->orderBy('c.id', 'ASC')
                ->setParameter('value', $value.'%')
                ->setParameter('type', $type)
                ->getQuery()
                ->getResult();
            }
            
        }

        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('myFriends', 'friendsWithMe', 'created', 'category'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($result, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/searchcontact/{searchType}/{p}", name="contact_serach", methods="POST")
     */
    public function searchContacts(Request $request, $searchType, $p): Response
    {
        $criteria = json_decode($request->getContent(), true); 
        // $criteria = [];

        // $return['relations'] = $this->getDoctrine()->getRepository(Contact::class)->getRelations($contact->getId());
        $limit=40;
        $offset = ($p-1)*$limit;
        $contacts;
        $contactRepo = $this->getDoctrine()->getRepository(Contact::class);

        switch ($searchType) {
            case 'name':
                $result = $contactRepo->getContactsByName($criteria['value'], $criteria, $offset, $limit);
                break;

            case 'id':
                $result = $contactRepo->getContactsById($criteria['value'], $criteria, $offset, $limit);
                break;

            case 'phone':
                $result = $contactRepo->getContactsByPhone($criteria['value'], $criteria, $offset, $limit);
                break;

            case 'fulltext':
                $result = $contactRepo->getContactsByFullText($criteria['value'], $criteria, $offset, $limit);
                break;

            case 'triangle':
                $result = $contactRepo->getContactsByTriangle($criteria['value'], $criteria, $offset, $limit);
                // dump($contacts);die;
                $this->getDefaultInfoForTriangle($result);
                break;
        }

        if($searchType != 'triangle'){
            $this->getDefaultInfo($result['contacts']);
        }
        

        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('myFriends', 'friendsWithMe', 'contacts', 'category', 'children', 'parent', 'created', '__initializer__', '__cloner__', '__isInitialized__'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $objec t->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($result, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    private function getDefaultInfo($contacts){
        // foreach ($contacts as $c) {
        //     foreach ($c->getInfos() as $i) {
        //         switch ($i->getType()) {
        //             case 'LandLine':
        //                 $c->setLandLine($i->getValue());
        //                 break;
        //             case 'Mobile':
        //                 $c->setMobile($i->getValue());
        //                 break;
        //             case 'Email':
        //                 $c->setEmail($i->getValue());
        //                 break;
        //         }
        //     }
        // }

        foreach ($contacts as $c) {
            foreach ($c->getInfos() as $i) {
                if($i->getType() != 'Mobile')
                    continue;
                
                if($i->getType() == 'Mobile' && $i->getStatus() == 1){
                    $c->setMobile($i->getValue());
                    break;
                }else{
                    $c->setMobile($i->getValue());
                }
            }

            foreach ($c->getInfos() as $i) {
                if($i->getType() != 'Email')
                    continue;
                
                if($i->getType() == 'Email' && $i->getStatus() == 1){
                    $c->setEmail($i->getValue());
                    break;
                }else{
                    $c->setEmail($i->getValue());
                }
            }
        }
    }

    private function getDefaultInfoForTriangle($contacts){

        foreach ($contacts as $contact) {
            foreach ($contact['contact']->getInfos() as $i) {
                if($i->getType() != 'Mobile')
                    continue;
                
                if($i->getType() == 'Mobile' && $i->getStatus() == 1){
                    $contact['contact']->setMobile($i->getValue());
                    break;
                }else{
                    $contact['contact']->setMobile($i->getValue());
                }
            }
    
            foreach ($contact['contact']->getInfos() as $i) {
                if($i->getType() != 'Email')
                    continue;
                
                if($i->getType() == 'Email' && $i->getStatus() == 1){
                    $contact['contact']->setEmail($i->getValue());
                    break;
                }else{
                    $contact['contact']->setEmail($i->getValue());
                }
            }
    
            foreach ($contact['relations'] as $c) {
                foreach ($c->getInfos() as $i) {
                    if($i->getType() != 'Mobile')
                        continue;
                    
                    if($i->getType() == 'Mobile' && $i->getStatus() == 1){
                        $c->setMobile($i->getValue());
                        break;
                    }else{
                        $c->setMobile($i->getValue());
                    }
                }
    
                foreach ($c->getInfos() as $i) {
                    if($i->getType() != 'Email')
                        continue;
                    
                    if($i->getType() == 'Email' && $i->getStatus() == 1){
                        $c->setEmail($i->getValue());
                        break;
                    }else{
                        $c->setEmail($i->getValue());
                    }
                }
            }
        }

        
    }
}
