<?php


namespace App\Application\DTO;

class CreateUser implements DataTransferObject
{

    public string $email;

    public string $password;

}
