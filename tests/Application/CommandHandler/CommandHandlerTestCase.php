<?php


namespace App\Tests\Application\CommandHandler;


use App\Application\Service\EventDispatcher;
use App\Domain\Event\Event;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\VarDumper;

abstract class CommandHandlerTestCase extends TestCase
{

    /**
     * @param string[] $events
     */
    protected function assertDispatchedEvents(EventDispatcher|MockObject $eventDispatcher, array $events){

        foreach ($events as $eventClass){
            $eventDispatcher->method('dispatch')
                ->willReturnCallback(fn(Event $event) => $this->assertInstanceOf($eventClass, $event));
        }

    }

}
