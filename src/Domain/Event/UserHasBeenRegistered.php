<?php


namespace App\Domain\Event;


use App\Domain\Entity\User;

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
