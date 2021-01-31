<?php


namespace App\Application\EventHandler;


use App\Domain\Event\UserHasBeenRegistered;

class SendNewUserNotification implements EventHandler
{

    public function __invoke(UserHasBeenRegistered $userHasBeenRegistered)
    {
        //TODO Send notification
    }

}
