<?php

namespace App\Tests\Application\Command;

use App\Application\Command\CreateUserCommand;
use App\Application\DTO\CreateUserDTO;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\Command\CreateUserCommand
 */
class CreateUserCommandTest extends TestCase
{

    private CreateUserCommand $command;

    protected function setUp(): void
    {
        $dto = new CreateUserDTO('user@test.com', 'secret');

        $this->command = new CreateUserCommand($dto);
    }

    /**
     * @covers ::__construct
     * @covers ::getPassword
     */
    public function testGetPassword()
    {
        $this->assertSame('secret', $this->command->getPassword());
    }

    /**
     * @covers ::getEmail
     */
    public function testGetEmail()
    {
        $this->assertSame('user@test.com', $this->command->getEmail());
    }
}
