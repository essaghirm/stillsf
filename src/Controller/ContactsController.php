<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Entity\Contact;
use App\Entity\Info;
use App\Entity\Relation;


class ContactsController extends Controller
{
    /**
     * @Route("/", name="contacts")
     */
    public function index(){
        $number = random_int(0, 100);
        dump($number);
        die();
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Contact::class);



        $contact = $repo->find(3);
        $friend = $repo->find(2);
        

        $relation = new Relation();

        $relation->setContact($contact);
        $relation->setFriend($friend);
        $em->persist($relation);

        // $info = new info();
        // $info->setType('LandLine');
        // $info->setLabel('Fix');
        // $info->setValue('0523214587');
        // $contact->addInfo($info);
        // $em->persist($info);

        $em->flush();


        // $contact = new contact();
        // $contact->setFname('Mouhcine');
        // $contact->setLname('ESSAGHIR');
        // $contact->setCity('Mohammedia');
        // $contact->setType('Contact');

        // $em->persist($contact);
        // $save = $em->flush();
        dump($contact);
        die();
        $json =  json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $response = new Response($json);
    	$response->headers->set('Content-Type', 'application/json');
    	return $response;
    }


    /**
     * @Route("/contacts", name="contacts_list")
     */
    public function allAction(SerializerInterface $serializer){
    	$contacts = $this->getDoctrine()
        ->getRepository(Contact::class)
        ->findAll();


        $jsonContent = $serializer->serialize($contacts, 'json');
        // $json =  json_encode($contacts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $response = new Response($jsonContent);
    	$response->headers->set('Content-Type', 'application/json');
    	return $response;
    }

    /**
     * @Route("/contact/{id}", name="contacts_list")
     */
    public function contactAction($id, SerializerInterface $serializer){
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Contact::class);
        $contact = $repo->find($id);

        $encoders = array(new XmlEncoder(), new JsonEncoder());

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('myFriends', 'friendsWithMe'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        // dump($contact);
        // die();
        $jsonContent = $serializer->serialize($contact, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
