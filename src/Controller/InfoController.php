<?php

namespace App\Controller;

use App\Entity\Info;
use App\Entity\Contact;
use App\Form\InfoType;
use App\Repository\InfoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/info")
 */
class InfoController extends Controller
{
    /**
     * @Route("/", name="info_index", methods="GET")
     */
    public function index(InfoRepository $infoRepository): Response
    {
        $infos = $infoRepository->findAll();

        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('contact'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($infos, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/new", name="info_new", methods="POST")
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $info = new Info();
        $data = json_decode($request->getContent(), true);

               
        $form = $this->createForm(InfoType::class, $info);
        $form->submit($data);

        // $info->setContact($this->getDoctrine()->getRepository(Contact::class)->find($data['contact_id']));

        $_infos = $this->getDoctrine()->getRepository(Info::class)->findBy(array(
            'contact' => $info->getContact(),
            'type' => $data['type']
        ));
        // dump($data['default']);

        if($data['status'] == true){
            foreach ($_infos as $i) {
                $i->setStatus(false);
            }
            $info->setStatus(true);
        }elseif($data['status'] == false){
            $info->setStatus(false);
        }


        // dump($info);
        // die;

        $errors = $validator->validate($info);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($info);
        $em->flush();

        // die('ok');

        return $this->forward('App\Controller\ContactController::show', array(
            'id'  => $data['contact']
        ));
    }

    /**
     * @Route("/{id}", name="info_show", methods="GET")
     */
    public function show(Info $info): Response
    {
        return $this->render('info/show.html.twig', ['info' => $info]);
    }

    /**
     * @Route("/{id}", name="info_edit", methods="PUT")
     */
    public function edit(Request $request, Info $info): Response
    {
        $data = json_decode($request->getContent(), true);      
        
        $info->setLabel($data['label']);
        $info->setValue($data['value']);
        $info->setType($data['type']);

        $_infos = $this->getDoctrine()->getRepository(Info::class)->findBy(array(
            'contact' => $info->getContact(),
            'type' => $data['type']
        ));
        // dump($data['default']);

        if($data['status'] == true){
            foreach ($_infos as $i) {
                $i->setStatus(false);
            }
            $info->setStatus(true);
        }elseif($data['status'] == false){
            $info->setStatus(false);
        }
        

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->forward('App\Controller\ContactController::show', array(
            'id'  => $info->getContact()->getId()
        ));
    }

    /**
     * @Route("/{id}", name="info_delete", methods="DELETE")
     */
    public function delete(Request $request, Info $info): Response
    {
        $contact_id = $info->getContact()->getId();
        $em = $this->getDoctrine()->getManager();
        
        $em->remove($info);
        $em->flush();

        return $this->forward('App\Controller\ContactController::show', array(
            'id'  => $contact_id
        ));
    }
}
