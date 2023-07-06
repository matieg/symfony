<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
// use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Annotation\Context;

class ExampleController extends AbstractController
{

    
    #[Route( path: '/index', name: 'app_index', methods: ['GET'])]
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->json([$users], context: [ 'groups' => ['user'] ]);
    }

    #[Route('/create', name: 'app_create')]
    public function create(Request $request, UserRepository $userRepository): JsonResponse
    {
        try {
            $user = new User();
            $user->setName($request->get('name'));
            $user->setUsername($request->get('username'));
            $user->setPassword($request->get('password'));
            
            $userRepository->save($user, true);
            return new JsonResponse('asdasdasd');
        } catch(Exception $e) {
            return new JsonResponse($e->getMessage());
        }
    }
}