<?php


namespace App\Application\CommandHandler;


use App\Domain\Event\Event;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

abstract class CommandHandler implements MessageHandlerInterface
{

    /**
     * @var Event[]
     */
    protected array $events = [];

    abstract protected function raiseEvents():void;

}
