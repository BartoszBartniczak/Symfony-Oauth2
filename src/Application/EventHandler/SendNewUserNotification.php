<?php


namespace App\Application\EventHandler;


use App\Domain\Event\UserHasBeenRegistered;
use Psr\Log\LoggerInterface;

class SendNewUserNotification implements EventHandler
{

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(UserHasBeenRegistered $userHasBeenRegistered)
    {
        $this->logger->info('New user has been registered');
    }

}
