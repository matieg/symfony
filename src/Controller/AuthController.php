<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route( path: '/register', name: 'register', methods: ['POST'])]
    public function register( #[MapRequestPayload()] User $user, UserRepository $userRepository, 
    UserPasswordHasherInterface $passwordHasher,
    ProfileRepository $profileRepository
    ): JsonResponse
    {
        try {

            $hashedPassword = $passwordHasher->hashPassword( $user, $user->getPassword() );
            $user->setPassword($hashedPassword);

            $profile = $profileRepository->find(1);
            $user->addProfile($profile);
            
            $userRepository->save($user, true);
            
            return $this->json( $user, Response::HTTP_OK, context: ['groups' => ['user']] );
            
        } catch (Exception $e ) {
            return $this->json( ['message'=> $e->getMessage()], Response::HTTP_BAD_REQUEST, [] );
        }
    }
    
    #[Route( path: '/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, AuthenticationUtils $authenticationUtils ): JsonResponse
    {
        try {

            $error = $authenticationUtils->getLastAuthenticationError();
            dump($request->getContent());
            dump($error);
            
            return $this->json( [], Response::HTTP_OK );
            
        } catch (Exception $e ) {
            return $this->json( ['message'=> $e->getMessage()], Response::HTTP_BAD_REQUEST, [] );
        }
    }
}