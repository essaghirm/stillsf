<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Category;

class CategoriesController extends Controller
{
    /**
     * @Route("/categories", name="categories")
     */
    public function index(Request $request)
    {
    	// $category = new Category();
     //    $category->setTitle('Institution public');

        // $entityManager = $this->getDoctrine()->getManager();
        // $entityManager->persist($category);
        // $entityManager->flush();

     //    $products = $this->getDoctrine()
	    // ->getRepository(Category::class)
	    // ->insertCategory(3, 'C2 PC1 - N2');
    	
        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
        ]);
    }

    /**
     * @Route("/category/insert/{parente}/{title}",
     * requirements={"parente": "\d+"},
     * name="categories")
     */
    public function insertCategory($parente, $title){
           $category = $this->getDoctrine()
        ->getRepository(Category::class)
        ->insertCategory(3, 'C2 PC1 - N2');
        die();
    }
}
