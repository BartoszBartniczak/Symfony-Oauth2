<?php


namespace App\Tests\Application\CommandHandler;


use App\Application\Service\EventDispatcher;
use App\Domain\Event\Event;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\VarDumper;

abstract class CommandHandlerTestCase extends TestCase
{

    public function assertDispatchedEvents(EventDispatcher|MockObject $eventDispatcher, array $expectedEvents)
    {

        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (array $events) use ($expectedEvents): void {

                $eventMap = array_map(function (Event $event) {
                    return get_class($event);
                }, $events);

                $this->assertSame($eventMap, $expectedEvents);

            });

    }

}
