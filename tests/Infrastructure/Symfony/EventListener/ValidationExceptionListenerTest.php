<?php

namespace App\Tests\Infrastructure\Symfony\EventListener;

use App\Infrastructure\Symfony\EventListener\ValidationExceptionListener;
use App\Infrastructure\Symfony\Exception\ValidationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @coversDefaultClass \App\Infrastructure\Symfony\EventListener\ValidationExceptionListener
 */
class ValidationExceptionListenerTest extends TestCase
{

    private ValidationExceptionListener $listener;

    protected function setUp(): void
    {
        $this->listener = new ValidationExceptionListener();
    }

    /**
     * @covers ::onKernelException
     */
    public function testOnKernelException()
    {

        $violationList = new ConstraintViolationList([
            $this->createViolationMock('username', 'Cannot be empty.'),
            $this->createViolationMock('username', 'To short.'),
            $this->createViolationMock('email', 'Cannot be empty.'),
        ]);

        $validationException = $this->createMock(ValidationException::class);
        $validationException->method('getConstraintViolationList')
            ->willReturn($violationList);

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);

        $exceptionEvent = new ExceptionEvent($kernel, $request, 1, $validationException);
        $this->listener->onKernelException($exceptionEvent);
        $response = $exceptionEvent->getResponse();
        assert($response instanceof JsonResponse);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('{"errorMessage":"Validation error","validationMessages":{"username":["Cannot be empty.","To short."],"email":["Cannot be empty."]}}', $response->getContent());
    }

    /**
     * @covers ::onKernelException
     */
    public function testOnKernelExceptionExitsIfExceptionIsNotValidationException()
    {
        
        $invalidArgumentException = $this->createMock(\InvalidArgumentException::class);

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);

        $exceptionEvent = new ExceptionEvent($kernel, $request, 1, $invalidArgumentException);
        $this->listener->onKernelException($exceptionEvent);
        $this->assertNull($exceptionEvent->getResponse());
    }

    /**
     * @return MockObject|ConstraintViolation
     */
    private function createViolationMock(string $path, string $message): MockObject|ConstraintViolation
    {
        $violation = $this->createMock(ConstraintViolation::class);
        $violation->method('getPropertyPath')->willReturn($path);
        $violation->method('getMessage')->willReturn($message);
        return $violation;
    }
}
