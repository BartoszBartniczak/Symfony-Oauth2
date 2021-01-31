<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\Command\CreateUserCommand;
use App\Application\CommandHandler\CreateUserHandler;
use App\Application\Service\EventDispatcher;
use App\Application\Service\UUIDService;
use App\Domain\Event\UserHasBeenRegistered;
use App\Domain\Repository\UserWriteRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @coversDefaultClass \App\Application\CommandHandler\CreateUserHandler
 */
class CreateUserHandlerTest extends CommandHandlerTestCase
{

    private CreateUserHandler $handler;
    private UserWriteRepository|MockObject $userRepository;
    private UserPasswordEncoderInterface|MockObject $passwordEncoder;
    private UUIDService|MockObject $uuidService;
    private EventDispatcher|MockObject $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserWriteRepository::class);
        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->uuidService = $this->createMock(UUIDService::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);

        $this->handler = new CreateUserHandler(
            $this->userRepository,
            $this->passwordEncoder,
            $this->uuidService,
            $this->eventDispatcher
        );
    }

    /**
     * @covers ::__invoke
     * @covers ::__construct
     */
    public function testInvoke()
    {
        $this->uuidService->expects($this->once())
            ->method('generate')
            ->willReturn('a56a6254-95b0-4544-a293-f5b873874995');

        $this->passwordEncoder->expects($this->once())
            ->method('encodePassword')
            ->willReturn('1S5G4ne5llJMPeJCCw8n/fif/GOiRTfYuXTAZo7oLac=');

        $this->userRepository->expects($this->once())
            ->method('saveNew');

        $this->assertDispatchedEvents($this->eventDispatcher, [UserHasBeenRegistered::class]);

        $command = $this->createMock(CreateUserCommand::class);
        $command->method('getEmail')
            ->willReturn('test@user.com');
        $command->method('getPassword')
            ->willReturn('secret');

        $this->handler->__invoke($command);
    }
}
