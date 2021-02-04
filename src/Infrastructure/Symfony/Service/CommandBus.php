<?php


namespace App\Infrastructure\Symfony\Service;


use App\Application\Command\Command;
use App\Application\Exception\CommandHandlerFailed;
use App\Application\Service\CommandBus as CommandBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class CommandBus implements CommandBusInterface
{

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function execute(Command $command): void
    {
        try {
            $this->messageBus->dispatch($command);
        }catch (HandlerFailedException $handlerFailedException){
            throw new CommandHandlerFailed('', null, $handlerFailedException);
        }
    }

}
