<?php

namespace App\UI\Web\Controller;

use App\Application\Command\CreateUserCommand;
use App\Application\DTO\CreateUserDTO;
use App\Infrastructure\Symfony\Service\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    private CommandBus $commandBus;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    public function __construct(CommandBus $commandBus, SerializerInterface $serializer)
    {
        $this->commandBus = $commandBus;
        $this->serializer = $serializer;
    }

    /**
     * @IsGranted("ROLE_OAUTH2_CLIENT_API")
     */
    #[Route('/user', name: 'user', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $userData = $this->serializer->serialize($this->getUser(), 'json', ['groups'=>['client-api']]);
        return new JsonResponse($userData, Response::HTTP_OK, [], true);
    }


    #[Route('/user', name: 'user_registration', methods: ['POST'])]
    public function register(CreateUserDTO $dto): JsonResponse
    {
        $this->commandBus->execute(new CreateUserCommand($dto));

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

}
