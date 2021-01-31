<?php

namespace App\UI\Web\Controller;

use App\Application\Command\CreateUserCommand;
use App\Application\DTO\CreateUserDTO;
use App\Domain\Exception\DomainException;
use App\Infrastructure\Symfony\Service\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
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
    public function register(CreateUserDTO $dto): JsonResponse
    {
        try {
            $this->commandBus->execute(new CreateUserCommand($dto));
        } catch (DomainException $exception) {
            die(__FILE__.':'.__LINE__);
        }

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

}
