<?php

namespace App\Tests\Infrastructure\Symfony\EventListener;

use App\Infrastructure\Symfony\EventListener\ExceptionListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \App\Infrastructure\Symfony\EventListener\ExceptionListener
 */
class ExceptionListenerTest extends TestCase
{

    private ExceptionListener $exceptionListener;
    private MockObject|LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->exceptionListener = new ExceptionListener($this->logger);
    }

    /**
     * @covers ::__construct
     * @covers ::onKernelException
     */
    public function testOnKernelException()
    {
        $exception = $this->createMock(\Exception::class);

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $exceptionEvent = new ExceptionEvent($kernel, $request, 1, $exception);

        $this->exceptionListener->onKernelException($exceptionEvent);
        $jsonResponse = $exceptionEvent->getResponse();
        $this->assertSame(500, $jsonResponse->getStatusCode());
        $this->assertSame('{"error":"Internal Server Error"}', $jsonResponse->getContent());

    }

    /**
     * @covers ::onKernelException
     */
    public function testOnKernelExceptionHandlesHttpExceptions()
    {
        $exception = $this->createMock(HttpExceptionInterface::class);
        $exception->method('getStatusCode')->willReturn(400);
        $exception->method('getHeaders')->willReturn(['CONTENT_TYPE'=>'application/json']);

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $exceptionEvent = new ExceptionEvent($kernel, $request, 1, $exception);

        $this->exceptionListener->onKernelException($exceptionEvent);
        $jsonResponse = $exceptionEvent->getResponse();
        $this->assertSame(400, $jsonResponse->getStatusCode());
        $this->assertSame('application/json', $jsonResponse->headers->get('content-type'));
    }

}
