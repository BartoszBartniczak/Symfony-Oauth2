<?php


namespace App\Domain\Query;


use App\Domain\Entity\User;
use App\Domain\Exception\User\UserDoesNotExist;

interface UserQuery
{

    /**
     * @throws UserDoesNotExist
     */
    public function findByEmail(string $email):User;

}
