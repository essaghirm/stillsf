<?php

namespace App\Controller;

use App\Entity\Relation;
use App\Entity\Contact;
use App\Form\RelationType;
use App\Repository\RelationRepository;
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
 * @Route("/relation")
 */
class RelationController extends Controller
{
    /**
     * @Route("/", name="relation_index", methods="GET")
     */
    public function index(RelationRepository $relationRepository): Response
    {
        return $this->render('relation/index.html.twig', ['relations' => $relationRepository->findAll()]);
    }

    

    /**
     * @Route("/new", name="relation_new", methods="POST")
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $relation = new Relation();
        $data = json_decode($request->getContent(), true);       
        
        $form = $this->createForm(RelationType::class, $relation);
        $form->submit($data);
        $relation->setOccupation($data['occupation']);
        $relation->setContact($this->getDoctrine()->getRepository(Contact::class)->find($data['contact_id']));
        $relation->setFriend($this->getDoctrine()->getRepository(Contact::class)->find($data['friend_id']));

        $errors = $validator->validate($relation);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($relation);
        $em->flush();

        return new JsonResponse("ok");

        dump($relation);
        die();

        return $this->redirectToRoute('relation_index');
        return $this->redirectToRoute('relation_show', array('id' => $relation->getId()));
    }

    /**
     * @Route("/{id}", name="relation_show", methods="GET")
     */
    public function show(Relation $relation): Response
    {
        return $this->render('relation/show.html.twig', ['relation' => $relation]);
    }

    /**
     * @Route("/{id}/edit", name="relation_edit", methods="GET|POST")
     */
    public function edit(Request $request, Relation $relation): Response
    {
        $form = $this->createForm(RelationType::class, $relation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('relation_edit', ['id' => $relation->getId()]);
        }

        return $this->render('relation/edit.html.twig', [
            'relation' => $relation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="relation_delete", methods="DELETE")
     */
    public function delete(Request $request, Relation $relation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$relation->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($relation);
            $em->flush();
        }

        return $this->redirectToRoute('relation_index');
    }
}
