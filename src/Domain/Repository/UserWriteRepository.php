<?php


namespace App\Domain\Repository;


use App\Domain\Entity\User;
use App\Domain\Exception\PersistenceException;
use App\Domain\Exception\User\UserAlreadyExists;

interface UserWriteRepository
{

    /**
     * @throws UserAlreadyExists
     * @throws PersistenceException
     */
    public function saveNew(User $user):void;

}
