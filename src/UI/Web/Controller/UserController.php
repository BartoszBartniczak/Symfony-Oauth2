<?php

namespace App\UI\Web\Controller;

use App\Application\DTO\CreateUser;
use App\Domain\Entity\User;
use App\Infrastructure\Symfony\Repository\UserRepository;
use App\Application\Service\UUIDService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\VarDumper\VarDumper;

class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var UUIDService
     */
    private UUIDService $uuidService;

    public function __construct(UserRepository $userRepository,
                                UserPasswordEncoderInterface $passwordEncoder,
                                UUIDService $UUIDService)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->uuidService = $UUIDService;
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
    public function register(CreateUser $dto): JsonResponse
    {

        $newUser = new User($this->uuidService->generate(), $dto->email, '');
        $newUser->setPassword($this->passwordEncoder->encodePassword($newUser, $dto->password));

        $this->userRepository->saveNew($newUser);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

}
