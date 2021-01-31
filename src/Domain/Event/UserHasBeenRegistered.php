<?php


namespace App\Domain\Event;


use App\Domain\Entity\User;

class UserHasBeenRegistered implements Event
{

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}
