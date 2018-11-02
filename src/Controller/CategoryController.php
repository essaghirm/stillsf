<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/", name="category_index", methods="GET")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(array('lvl' => 1));
        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('contacts', 'parent', 'children'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($categories, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}/{lvl}", name="category_by_lvl", methods="GET")
     */
    public function byLvl($id, $lvl): Response
    {

        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $array['categories'] = $categoryRepository->findBy(array('lvl' => $lvl, 'parent' => $id));
        $array['details'] = $categoryRepository->getParentDetails($id);
        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('contacts', 'parent', 'children'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($array, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/new", name="category_new", methods="POST")
     */
    public function new(Request $request): Response
    {
        $data = json_decode($request->getContent(), true); 
        $category = $this->getDoctrine()->getRepository(Category::class)->insertCategory($data['parent'], $data['title']);
        return $this->forward('App\Controller\CategoryController::show', array(
            'id'  => $category->getId()
        ));
    }

    /**
     * @Route("/{id}", name="category_show", methods="GET")
     */
    public function show(Category $category): Response
    {
        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('contacts', 'children'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($category, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}", name="category_edit", methods="PUT")
     */
    public function edit(Request $request, Category $category): Response
    {
        $data = json_decode($request->getContent(), true);
        $category->setTitle($data['title']);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->forward('App\Controller\CategoryController::show', array(
            'id'  => $category->getId()
        ));
    }

    /**
     * @Route("/{id}", name="category_delete", methods="DELETE")
     */
    public function delete(Request $request,CategoryRepository $categoryRepository, Category $category): Response
    {
        $left = $category->getLft();
        $lvl = $category->getLvl();
        $parent_id = $category->getParent()->getId();
        // dump($parent_id, $left, $lvl, $category);

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $result = $this->getDoctrine()->getRepository(Category::class)->updateAfterRemoveCategory($left);
        return $this->forward('App\Controller\CategoryController::byLvl', array(
            'id'  => $parent_id,
            'lvl' => $lvl
        ));
        // die;

        // $em = $this->getDoctrine()->getManager();
        // $em->remove($category);
        // $em->flush();
    }
}
