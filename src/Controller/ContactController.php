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
     * @Route("/", name="contact_index", methods="GET")
     */
    public function index(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();

        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('myFriends', 'friendsWithMe'));

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
     * @Route("/new", name="contact_new", methods="POST")
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

        return $this->redirectToRoute('contact_show', array('id' => $contact->getId()));
        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="contact_show", methods="GET")
     */
    public function show(Contact $contact): Response
    {

        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('myFriends', 'friendsWithMe', 'contacts'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($contact, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}/edit", name="contact_edit", methods="GET|POST")
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
}
