<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/user', name: 'user', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/user', name: 'user_registration', methods: ['POST'])]
    public function register(Request $request):JsonResponse{
        $requestData = json_decode($request->getContent(), true);

        $newUser = new User();
        $newUser->setEmail($requestData['email']);
        $newUser->setPassword($this->passwordEncoder->encodePassword($newUser, $requestData['password']));

        $this->userRepository->saveNew($newUser);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

}
