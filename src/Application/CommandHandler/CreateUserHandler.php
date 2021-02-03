<?php


namespace App\Application\CommandHandler;


use App\Application\Command\CreateUserCommand;
use App\Application\Service\EventDispatcher;
use App\Application\Service\UUIDService;
use App\Domain\Entity\User;
use App\Domain\Event\UserHasBeenRegistered;
use App\Domain\Repository\UserWriteRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserHandler extends CommandHandler
{
    private UserWriteRepository $userRepository;
    private UserPasswordEncoderInterface $passwordEncoder;
    private UUIDService $uuidService;
    private EventDispatcher $eventDispatcher;

    public function __construct(UserWriteRepository $userRepository,
                                UserPasswordEncoderInterface $passwordEncoder, //TODO password service
                                UUIDService $uuidService,
                                EventDispatcher $eventDispatcher)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->uuidService = $uuidService;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateUserCommand $command)
    {
        $id = $this->uuidService->generate();
        $user = new User($id, $command->getEmail(), '', ['ROLE_USER']);
        $user->changePassword($this->passwordEncoder->encodePassword($user, $command->getPassword()));

        $this->userRepository->saveNew($user);
        $this->eventDispatcher->dispatch(new UserHasBeenRegistered($user->getId()));
    }


}
