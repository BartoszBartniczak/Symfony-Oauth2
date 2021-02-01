<?php


namespace App\Domain\Event;

class UserHasBeenRegistered implements Event
{

    private string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

}
