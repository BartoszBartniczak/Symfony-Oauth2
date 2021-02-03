<?php

namespace App\Tests\Application\DTO;

use App\Application\DTO\CreateUserDTO;
use App\Application\DTO\DataTransferObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\DTO\CreateUserDTO
 */
class CreateUserDTOTest extends TestCase
{

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $dto = new CreateUserDTO('test@user.com', 'password');
        $this->assertInstanceOf(DataTransferObject::class, $dto);
        $this->assertSame('test@user.com', $dto->email);
        $this->assertSame('password', $dto->password);
    }
}
