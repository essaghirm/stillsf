<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_index", methods="GET")
     */
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setIgnoredAttributes(array('myFriends', 'friendsWithMe', 'created', 'category'));

        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            // return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($users, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/new", name="user_new", methods="POST")
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $user = new User();
        $data = json_decode($request->getContent(), true);       
        
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);
        $user->setPassword(md5($data['password']));
        $user->setStatus(0);
        $user->setRoles("user");

        // dump($user);
        // die();

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }

        $_user = $this->getDoctrine()->getEntityManager()->createQueryBuilder()->select('u')
        ->from('App\Entity\User', 'u')
        ->where('u.user_name like :user_name OR u.email like :user_name')
        ->setParameter('user_name', $data['user_name'])
        ->getQuery()->getOneOrNullResult();

        if($_user){
            $array = [];
            $array['status'] = 'error';
            $array['message'] = "Ce nom d'utilisateurou existe déjà";
            return new JsonResponse($array);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();


        // $encoders = array(new JsonEncoder());
        // $normalizer = new ObjectNormalizer();
        // $normalizer->setCircularReferenceLimit(0);
        // $normalizer->setIgnoredAttributes(array('myFriends', 'friendsWithMe', 'created', 'category'));

        // // Add Circular reference handler
        // $normalizer->setCircularReferenceHandler(function ($object) {
        //     // return $object->getId();
        // });
        // $normalizers = array($normalizer);
        // $serializer = new Serializer($normalizers, $encoders);

        // $jsonContent = $serializer->serialize($user, 'json');
        // $response = new Response($jsonContent);
        // $response->headers->set('Content-Type', 'application/json');
        // return $response;
        $array = [];
        $array['status'] = 'created';
        $array['message'] = "Votre compte a bien été créé, contacter l'administrateur pour activé votre compte";
        return new JsonResponse($array);
    }

    /**
     * @Route("/{id}", name="user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        return true;
    }

    /**
     * @Route("/login", name="user_login", methods="POST")
     */
    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true); 
        // $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array(
        //     'user_name' => $data['user_name'],
        //     'password' => md5($data['password'])
        // ));

        $user = $this->getDoctrine()->getEntityManager()->createQueryBuilder()->select('u')
        ->from('App\Entity\User', 'u')
        ->where('u.user_name like :user_name OR u.email like :user_name')
        ->andWhere('u.password = :password')
        ->setParameter('user_name', $data['user_name'])
        ->setParameter('password', md5($data['password']))
        ->getQuery()->getOneOrNullResult();

        if($user && $user->getStatus() == 1){
            $array = [];
            $array['status'] = true;
            $array['user'] = $user;
            $encoders = array(new JsonEncoder());
            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceLimit(0);
            $normalizer->setIgnoredAttributes(array('password'));

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
        }elseif($user && $user->getStatus() == 0){
            $array = [];
            $array['status'] = 'error';
            $array['message'] = "Votre compte n'est pas encore activé, Merci de contacter l'administrateur !";
            return new JsonResponse($array);
        }else{
            $array = [];
            $array['status'] = 'error';
            $array['message'] = "Le nom d'utilisateur ou le mot de passe est incorrect !";
            return new JsonResponse($array);
        }
    }

    /**
     * @Route("/changeuserstatus/{id}", name="user_changestatus", methods="POST")
     */
    public function changeuserstatus(Request $request, User $user): Response
    {
        $data = json_decode($request->getContent(), true); 
        $user->setStatus($data['status']);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return new JsonResponse(true);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods="POST")
     */
    public function edit(Request $request, User $user): Response
    {
        return true;
    }

    /**
     * @Route("/{id}", name="user_delete", methods="DELETE")
     */
    public function delete(Request $request, User $user): Response
    {
        return true;
    }
}
