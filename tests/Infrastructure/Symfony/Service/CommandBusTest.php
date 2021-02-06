<?php

namespace App\Tests\Infrastructure\Symfony\Service;

use App\Application\Command\Command;
use App\Application\Exception\CommandHandlerFailed;
use App\Infrastructure\Symfony\Service\CommandBus;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @coversDefaultClass \App\Infrastructure\Symfony\Service\CommandBus
 */
class CommandBusTest extends TestCase
{

    private MessageBusInterface $messageBus;
    private CommandBus $commandBus;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->commandBus = new CommandBus($this->messageBus);
    }

    /**
     * @covers ::execute
     * @covers ::__construct
     */
    public function testExecute()
    {
        $command = $this->createMock(Command::class);

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn(new Envelope($command));

        $this->commandBus->execute($command);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteThrowsExceptionIfHandlerFailed(){
        $this->expectException(CommandHandlerFailed::class);
        
        $command = $this->createMock(Command::class);
        $handlerFailedException = $this->createMock(HandlerFailedException::class);
        
        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willThrowException($handlerFailedException);
        
        $this->commandBus->execute($command);
        
        $expectedException = $this->getExpectedException();
        assert($expectedException instanceof CommandHandlerFailed);
        $this->assertSame($handlerFailedException, $expectedException->getPrevious());
    }
}
    
