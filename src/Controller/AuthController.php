<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
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
    public function login(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, JWTEncoderInterface $jwtManager): JsonResponse
    {
        try {

            $dataRequest = $request->toArray();
            $user = $userRepository->findOneBy([ 'username' => $dataRequest['username']]);

            if(!$user)
                throw new Exception('Usuario invalido');
                
            if( !$passwordHasher->isPasswordValid($user, $dataRequest['password']) ) 
                throw new Exception('Usuario invalido (password)');

                
            dump($_ENV['JWT_SECRET_KEY']);
            $token = $jwtManager->encode(['username' => $dataRequest['username'] ]);
            dump($token);
            
            return $this->json( [], Response::HTTP_OK );
            
        } catch (Exception $e ) {
            return $this->json( ['message'=> $e->getMessage()], Response::HTTP_BAD_REQUEST, [] );
        }
    }

    #[Route( path: '/api/token/validate', name: 'token_validate', methods: ['POST'])]
    public function validateToken(Request $request): JsonResponse
    {
        // Obtener el token enviado en la solicitud
        $token = $request->headers->get('Authorization');

        // Realizar la lógica de validación del token
        if ($this->isValidToken($token)) {
            return new JsonResponse(['message' => 'Token válido']);
        }

        // Devolver una respuesta de error si el token no es válido
        return new JsonResponse(['message' => 'Token inválido'], 401);
    }

    private function isValidToken(string $token): bool
    {
        // Implementar la lógica de validación del token según tus requerimientos
        // Puede ser una verificación en una base de datos, una lista blanca, etc.
        // En este ejemplo, simplemente verificamos si el token es igual a 'mi_token_secreto'
        return $token === 'mi_token_secreto';
    }

}