<?php


namespace App\Infrastructure\Symfony\Service;


use App\Application\Exception\CommandHandlerFailed;
use App\Application\Service\EventDispatcher as EventDispatcherInterface;
use App\Domain\Event\Event;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
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
        try {
            $this->messageBus->dispatch($event);
        }catch (HandlerFailedException $handlerFailedException){
            throw new CommandHandlerFailed('', null, $handlerFailedException);
        }
    }

}
