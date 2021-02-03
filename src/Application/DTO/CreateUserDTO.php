<?php


namespace App\Application\DTO;

final class CreateUserDTO implements DataTransferObject
{

    public function __construct(
        public string $email,
        public string $password,
    )
    {
    }


}
