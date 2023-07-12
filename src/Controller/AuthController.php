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
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
// use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;

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
    public function login(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        try {

            $dataRequest = $request->toArray();
            $user = $userRepository->findOneBy([ 'username' => $dataRequest['username']]);

            if(!$user)
                throw new Exception('Usuario invalido');
                
            if( !$passwordHasher->isPasswordValid($user, $dataRequest['password']) ) 
                throw new Exception('Usuario invalido (password)');

            $token = $jwtManager->create(['username' => $dataRequest['username'] ]);
            dump($token);
            
            return $this->json( [], Response::HTTP_OK );
            
        } catch (Exception $e ) {
            return $this->json( ['message'=> $e->getMessage()], Response::HTTP_BAD_REQUEST, [] );
        }
    }
}