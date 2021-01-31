<?php


namespace App\Application\EventHandler;


use App\Domain\Event\UserHasBeenRegistered;

class SendWelcomeMessage implements EventHandler
{

    public function __invoke(UserHasBeenRegistered $hasBeenRegistered)
    {
        // TODO: Send email message
    }

}
