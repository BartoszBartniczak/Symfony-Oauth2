<?php


namespace App\Infrastructure\Symfony\Service;


use App\Application\Service\EventDispatcher as EventDispatcherInterface;
use App\Domain\Event\Event;
use Symfony\Component\Messenger\MessageBusInterface;

class EventDispatcher implements EventDispatcherInterface
{

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function dispatch(Event $event): void
    {
        $this->messageBus->dispatch($event);
    }

}
