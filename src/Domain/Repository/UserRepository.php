<?php


namespace App\Domain\Repository;


use App\Domain\Entity\User;

interface UserRepository
{

    public function saveNew(User $user):void;

}
