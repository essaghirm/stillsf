<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $categories = $categoryRepository->findBy(array('lvl' => $lvl, 'parent' => $id));
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
     * @Route("/new", name="category_new", methods="POST")
     */
    public function new(Request $request): Response
    {
        $data = json_decode($request->getContent(), true); 
        $category = $this->getDoctrine()
        ->getRepository(Category::class)
        ->insertCategory($data['parent'], $data['title']);
        return $this->redirectToRoute('category_show', array('id' => $category->getId()));
    }

    /**
     * @Route("/{id}", name="category_show", methods="GET")
     */
    public function show(Category $category): Response
    {
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

        $jsonContent = $serializer->serialize($category, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods="GET|POST")
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_edit', ['id' => $category->getId()]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods="DELETE")
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('category_index');
    }
}
