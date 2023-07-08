<?php
namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route( path: '/register', name: 'register', methods: ['GET'])]
    public function index()
    {
        // try {

        //     $userRepository->save($user, true);
        //     return $this->json( $user, Response::HTTP_OK );
            
        // } catch (Exception $e ) {
        //     return $this->json( ['message'=> $e->getMessage()], Responsee::HTTP_BAD_REQUEST );
        // }
    }
}