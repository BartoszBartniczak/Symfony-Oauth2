<?php

namespace App\Tests\Infrastructure\Symfony\Exception;

use App\Infrastructure\Exception\InvalidArgumentException;
use App\Infrastructure\Symfony\Exception\ValidationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @coversDefaultClass \App\Infrastructure\Symfony\Exception\ValidationException
 */
class ValidationExceptionTest extends TestCase
{

    private ValidationException $exception;
    private ConstraintViolationList|MockObject $constraintViolationList;

    protected function setUp(): void
    {
        $this->constraintViolationList = $this->createMock(ConstraintViolationList::class);
        $this->exception = new ValidationException($this->constraintViolationList);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(InvalidArgumentException::class, $this->exception);
    }
    
    /**
     * @covers ::getConstraintViolationList
     */
    public function testGetConstraintViolationList()
    {
        $this->assertSame($this->constraintViolationList, $this->exception->getConstraintViolationList());
    }

   
}
