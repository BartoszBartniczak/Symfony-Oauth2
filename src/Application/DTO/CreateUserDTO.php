<?php


namespace App\Application\DTO;

final class CreateUserDTO implements DataTransferObject
{

    public string $email;

    public string $password;

}
