<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// use Symfony\Component\Serializer\SerializerInterface;

class ExampleController extends AbstractController
{
    
    #[Route( path: '/index', name: 'app_index', methods: ['GET'])]
    // #[IsGranted('token', message: 'No access! Get out!')]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        // return $this->json($users, 200);
        return $this->json($users, context: [ 'groups' => ['user'] ]);
    }

    #[Route('/view/{id}')]
    public function show( #[MapEntity()] User $user ): JsonResponse
    {

        return $this->json($user, context: [ 'groups' => ['user'] ]);
    }

    #[Route( path:'/create', name: 'app_create', methods: ['POST', 'GET'] )]
    public function create( #[MapRequestPayload()] User $user, UserRepository $userRepository, 
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

    #[Route( path: '/update/{id}', name: 'app_update' , methods: ['put'])]
    public function update( int $id, #[MapRequestPayload()] User $userRequest, UserRepository $userRepository): JsonResponse
    {
        try{
            $user = $userRepository->find($id);
            if(!$user)
                throw new Exception('Usuario no encontrado');
            
            $user->setName( $userRequest->getName());
            $user->setUsername( $userRequest->getUsername());
            $userRepository->save($user, true);
            return $this->json([ 'message' => 'Usuario modificado', Response::HTTP_OK]);

        } 
        catch( Exception $e ) {
            return $this->json( ['message'=> $e->getMessage()], Response::HTTP_BAD_REQUEST );
        }
    }

    #[Route( path: '/delete/{id}', name: 'app_delete' , methods: ['delete'])]
    public function delete($id, UserRepository $userRepository): JsonResponse
    {
        try{
            $user = new User();
            $user = $userRepository->find($id);
            if(!$user)
                throw new Exception('Usuario no encontrado');

            $userRepository->remove($user, true);
            return $this->json([ 'message' => 'Usuario eliminado', Response::HTTP_OK]);

        } catch( Exception $e ) {
            return $this->json( ['message'=> $e->getMessage()], Response::HTTP_BAD_REQUEST );
        }
    }

    // #[Route( path:'/create', name: 'app_create', methods: ['POST', 'GET'] )]
    // public function create(Request $request, UserRepository $userRepository)
    // {

    //     try{
    //         // $data = json_decode($request->getContent(), true);
    //         // dump($data);
    //         $user = new User();
    //         $form = $this->createForm( UserFormType::class, $user );
    //         // $form->handleRequest($data);
    //         $form->submit($request->getContent());
    //         print_r($form->getData());
    //         if( $form->isSubmitted() ){
    //             $userRepository->save($user, true);
    //             return $this->json( $user, Response::HTTP_OK );
    //         }
    //         dump("---------------------");
    //         return $this->json($form, 200);
    //     } catch (Exception $e ){
    //         dump($e);
    //         return $this->json( ['message'=> $e->getMessage()], Response::HTTP_OK );
    //     }
    // }
}