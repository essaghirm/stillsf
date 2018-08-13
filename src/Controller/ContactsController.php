<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Contact;


class ContactsController extends Controller
{
    /**
     * @Route("/", name="contacts")
     */
    public function index(){
        // $number = random_int(0, 100);

        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $contact = new contact();
        $contact->setFname('Mouhcine');
        $contact->setLname('ESSAGHIR');
        $contact->setCity('Mohammedia');
        $contact->setType('Contact');

        $entityManager->persist($contact);
        $entityManager->flush();

        $json =  json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $response = new Response($json);
    	$response->headers->set('Content-Type', 'application/json');
    	return $response;
    }


    /**
     * @Route("/contacts", name="contacts_list")
     */
    public function show(SerializerInterface $serializer){
    	$contacts = $this->getDoctrine()
        ->getRepository(Contact::class)
        ->findAll();


        $jsonContent = $serializer->serialize($contacts, 'json');
        // $json =  json_encode($contacts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $response = new Response($jsonContent);
    	$response->headers->set('Content-Type', 'application/json');
    	return $response;
    }
}
