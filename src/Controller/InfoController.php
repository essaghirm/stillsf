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
     * @Route("/new", name="info_new", methods="GET|POST")
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $info = new Info();
        $data = json_decode($request->getContent(), true);       
        
        $form = $this->createForm(InfoType::class, $info);
        $form->submit($data);
        $info->setContact($this->getDoctrine()->getRepository(Contact::class)->find($data['contact_id']));

        $errors = $validator->validate($info);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($info);
        $em->flush();

        return $this->redirectToRoute('info_index');
        return $this->redirectToRoute('info_show', array('id' => $info->getId()));
    }

    /**
     * @Route("/{id}", name="info_show", methods="GET")
     */
    public function show(Info $info): Response
    {
        return $this->render('info/show.html.twig', ['info' => $info]);
    }

    /**
     * @Route("/{id}/edit", name="info_edit", methods="GET|POST")
     */
    public function edit(Request $request, Info $info): Response
    {
        $form = $this->createForm(InfoType::class, $info);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('info_edit', ['id' => $info->getId()]);
        }

        return $this->render('info/edit.html.twig', [
            'info' => $info,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="info_delete", methods="DELETE")
     */
    public function delete(Request $request, Info $info): Response
    {
        if ($this->isCsrfTokenValid('delete'.$info->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($info);
            $em->flush();
        }

        return $this->redirectToRoute('info_index');
    }
}
