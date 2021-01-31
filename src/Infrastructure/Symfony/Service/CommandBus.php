<?php


namespace App\Infrastructure\Symfony\Service;


use App\Application\Command\Command;
use App\Application\Service\CommandBus as CommandBusInterface;
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
        $this->messageBus->dispatch($command);
    }

}
