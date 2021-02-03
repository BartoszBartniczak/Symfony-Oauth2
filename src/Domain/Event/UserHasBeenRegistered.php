<?php


namespace App\Domain\Event;

class UserHasBeenRegistered implements Event
{

    public function __construct(private string $userId)
    {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

}
