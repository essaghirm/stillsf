<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\CatCont;
use App\Entity\OldCategory;
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
 * @Route("/diagnostic")
 */
class DiagnosticController extends Controller
{
    /**
     * @Route("/", name="category_setup", methods="GET")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $oldCategoryRepository = $this->getDoctrine()->getRepository(OldCategory::class);
        $old1 = $oldCategoryRepository->findBy(array('table' => 4));
        dump(sizeof($old1));
        // die();

        foreach ($old1 as $old) {
            $parent = $categoryRepository->findBy(array('lvl' => $old->getTable(), 'old_id' => $old->getParent()));

            dump($parent[0]->getId().' - '.$old->getOldId().' - '.$old->getTitle());
            // dump($parent);
            $categoryRepository->insertCategory($parent[0]->getId(), $old->getTitle(), $old->getOldId());

        }
        

        // $categoryRepository->insertCategory();
        dump('ok');
        die();
    }

    /**
     * @Route("/cat_to_contact", name="category_to_contact", methods="GET")
     */
    public function cat_to_contact(CategoryRepository $categoryRepository): Response
    {
        // $em = $this->getDoctrine();
        // $categoryRepo = $em->getRepository(Category::class);
        // $contactRepo = $em->getRepository(Contact::class);
        // $catContRepo = $em->getRepository(CatCont::class);
        // $contacts = $contactRepo->findAll();
        // dump(sizeof($contacts));
        // // die();

        // foreach ($contacts as $c) {
        //     $old_cat = $catContRepo->findBy(array('contact' => $c->getId()));
        //     if($old_cat){
        //         $new_cat = $categoryRepo->findBy(array('lvl' => 5, 'old_id' => $old_cat[0]->getCategory()));
        //         dump($c->getId() .' -- '. $old_cat[0]->getCategory() .' -- '. $new_cat[0]->getTitle());
        //         $c->setCategory($new_cat[0]);
        //     }
        // }

        // $em->getManager()->flush();



        // $categoryRepository->insertCategory();
        dump('ok');
        die();
    }

}