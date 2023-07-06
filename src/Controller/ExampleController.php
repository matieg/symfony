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

    #[Route( path:'/create', name: 'app_create', methods: ['POST'] )]
    public function create(Request $request, UserRepository $userRepository)
    {

        try{
            // json_decode($request->getContent(), true);
            $user = new User();
            $form = $this->createForm( UserFormType::class, $user );
            $form->handleRequest($request);
            if( $form->isSubmitted() && $form->isValid() ){
                $userRepository->save($user, true);
                return $this->json( ['message'=>'mensaje enviado'], Response::HTTP_OK );
            }
        } catch (Exception $e ){

            return $this->json( ['message'=> $e->getMessage()], Response::HTTP_OK );
        }
    }
}