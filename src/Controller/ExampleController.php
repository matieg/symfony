<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserFormType;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Serializer\SerializerInterface;
// use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
// use Symfony\Component\Serializer\Annotation\Context;

class ExampleController extends AbstractController
{

    
    #[Route( path: '/index', name: 'app_index', methods: ['GET'])]
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->json($users, 200);
        // return $this->json($users, context: [ 'groups' => ['user'] ]);
    }

    #[Route( path:'/create', name: 'app_create', methods: ['POST', 'GET'] )]
    public function create(Request $request, UserRepository $userRepository)
    {

        try{
            // $data = json_decode($request->getContent(), true);
            // dump($data);
            $user = new User();
            $form = $this->createForm( UserFormType::class, $user );
            // $form->handleRequest($request);
            $form->submit($request->getContent());
            print_r($form->getData());
            if( $form->isSubmitted() ){
                $userRepository->save($user, true);
                return $this->json( $user, Response::HTTP_OK );
            }
            dump("---------------------");
            return $this->json($form, 200);
        } catch (Exception $e ){
            dump($e);
            return $this->json( ['message'=> $e->getMessage()], Response::HTTP_OK );
        }
    }
}