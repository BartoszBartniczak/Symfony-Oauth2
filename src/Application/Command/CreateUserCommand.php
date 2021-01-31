<?php


namespace App\Application\Command;


use App\Application\DTO\CreateUserDTO;

class CreateUserCommand implements Command
{

    private CreateUserDTO $dto;

    public function __construct(CreateUserDTO $dto)
    {
        $this->dto = $dto;
    }

    public function getDto(): CreateUserDTO
    {
        return $this->dto;
    }

}
