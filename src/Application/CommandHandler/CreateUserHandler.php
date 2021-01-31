<?php


namespace App\Application\CommandHandler;


use App\Application\Command\CreateUserCommand;
use App\Application\Service\EventDispatcher;
use App\Application\Service\UUIDService;
use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserHandler extends CommandHandler
{
    private UserRepository $userRepository;
    private UserPasswordEncoderInterface $passwordEncoder;
    private UUIDService $uuidService;
    private EventDispatcher $eventDispatcher;

    public function __construct(UserRepository $userRepository,
                                UserPasswordEncoderInterface $passwordEncoder,
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
        $user = new User($id, $command->getDto()->email, '');
        $user->changePassword($this->passwordEncoder->encodePassword($user, $command->getDto()->password));

        $this->userRepository->saveNew($user);
        $this->events = $user->raiseEvents();
    }

    protected function raiseEvents(): void
    {
        $this->eventDispatcher->dispatch($this->events);
    }

}
