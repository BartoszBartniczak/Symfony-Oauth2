<?php

namespace App\Tests\Domain\Exception;

use App\Domain\Exception\DomainException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Domain\Exception\DomainException
 */
class DomainExceptionTest extends TestCase
{

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $domainException = new DomainException();

        $this->assertInstanceOf(\DomainException::class, $domainException);
    }
}
