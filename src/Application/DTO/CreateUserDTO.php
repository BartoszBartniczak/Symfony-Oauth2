<?php


namespace App\Application\DTO;

class CreateUserDTO implements DataTransferObject
{

    public string $email;

    public string $password;

}
