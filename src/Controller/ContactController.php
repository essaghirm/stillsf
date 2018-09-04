<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Category;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/contact")
 */
class ContactController extends Controller
{
    /**
     * @Route("/p/{p}", name="contact_index", methods="GET")
     */
    public function index(ContactRepository $contactRepository, $p): Response
    {
        $nb=20;
        $p = ($p-1)*$nb;
        $contacts = $contactRepository->findBy(array(), array('id' => 'DESC'), $nb, $p);

        foreach ($contacts as $c) {
            foreach ($c->getInfos() as $i) {
                // if($i->getType() == "LandLine" || $i->getType() == "Mobile"){
                //     $c->setPhone($i->getValue());
                // }

                // if($i->getType() == "Email"){
                //     $c->setEmail($i->getValue());
                // }

                // if($i->getType() == "Email"){
                //     $c->setEmail($i->getValue());
                // }

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

        // die();

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
        // dump($data);
        // dump($contact);
        // die();
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
    
        // ... further modify the response or return it directly
    
        return $response;
    }

    /**
     * @Route("/{id}", name="contact_show", methods="GET")
     */
    public function show(Contact $contact): Response
    {
        // dump($contact);
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
        // dump($contact);
        $return = [];
        // $return['contact'] = $contact;
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
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contact_edit', ['id' => $contact->getId()]);
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="contact_delete", methods="DELETE")
     */
    public function delete(Request $request, Contact $contact): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush();
        }

        return $this->redirectToRoute('contact_index');
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
     * @Route("/searchcontact/{searchType}/{value}", name="contact_serach", methods="POST")
     */
    public function searchContacts(Request $request, $searchType, $value): Response
    {
        $criteria = json_decode($request->getContent(), true); 
        // $criteria = [];

        // $return['relations'] = $this->getDoctrine()->getRepository(Contact::class)->getRelations($contact->getId());

        $contacts;
        $contactRepo = $this->getDoctrine()->getRepository(Contact::class);

        switch ($searchType) {
            case 'name':
                $contacts = $contactRepo->getContactsByName($value, $criteria);
                break;

            case 'id':
                $contacts = $contactRepo->getContactsById($value, $criteria);
                break;

            case 'phone':
                $contacts = $contactRepo->getContactsByPhone($value, $criteria);
                break;

            case 'fulltext':
                $contacts = $contactRepo->getContactsByText($value, $criteria);
                break;
            
            default:
                # code...
                break;
        }

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

        $jsonContent = $serializer->serialize($contacts, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
