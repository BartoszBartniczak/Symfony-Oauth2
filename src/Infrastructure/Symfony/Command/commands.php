<?php

use App\Application\Command\CreateUserCommand;
use App\Application\DTO\CreateUserDTO;

$newUserDto = new CreateUserDTO('test@user.com', 'zaq12wsx');
return [
    new CreateUserCommand($newUserDto)
];
