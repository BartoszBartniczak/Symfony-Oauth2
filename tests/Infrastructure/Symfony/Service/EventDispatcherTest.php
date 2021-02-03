<?php

namespace App\Tests\Infrastructure\Symfony\Service;

use App\Domain\Event\Event;
use App\Infrastructure\Symfony\Service\EventDispatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @coversDefaultClass \App\Infrastructure\Symfony\Service\EventDispatcher
 */
class EventDispatcherTest extends TestCase
{

    private EventDispatcher $eventDispatcher;
    private MessageBusInterface|MockObject $messageBus;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->eventDispatcher = new EventDispatcher($this->messageBus);
    }

    /**
     * @covers ::dispatch
     * @covers ::__construct
     */
    public function testDispatch()
    {
        $event = $this->createMock(Event::class);

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $this->eventDispatcher->dispatch($event);
    }
}
