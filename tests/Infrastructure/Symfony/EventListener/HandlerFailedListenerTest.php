<?php

namespace App\Tests\Infrastructure\Symfony\EventListener;

use App\Application\Exception\CommandHandlerFailed;
use App\Domain\Exception\DomainException;
use App\Domain\Exception\User\UserAlreadyExists;
use App\Infrastructure\Symfony\EventListener\HandlerFailedListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

/**
 * @coversDefaultClass \App\Infrastructure\Symfony\EventListener\HandlerFailedListener
 */
class HandlerFailedListenerTest extends TestCase
{

    private HandlerFailedListener $handler;

    protected function setUp(): void
    {
        $this->handler = new HandlerFailedListener();
    }

    /**
     * @covers ::onKernelException
     * @covers ::getMessage
     */
    public function testOnKernelException()
    {
        $domainException = new DomainException('Test message');
        
        $envelope = new Envelope(new \stdClass());
        $handlerFailedException = new HandlerFailedException($envelope, [$domainException]);
        
        $commandHandlerFailed = new CommandHandlerFailed('', null, $handlerFailedException);
        
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $exceptionEvent = new ExceptionEvent($kernel, $request, 1, $commandHandlerFailed);
        
        $this->handler->onKernelException($exceptionEvent);
        
        $response = $exceptionEvent->getResponse();
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('{"errorMessage":"Test message"}', $response->getContent());
    }

    /**
     * @covers ::onKernelException
     * @covers ::getMessage
     */
    public function testOnKernelExceptionCreatesMessageFromExceptionType()
    {
        $domainException = new UserAlreadyExists();

        $envelope = new Envelope(new \stdClass());
        $handlerFailedException = new HandlerFailedException($envelope, [$domainException]);
        $commandHandlerFailed = new CommandHandlerFailed('', null, $handlerFailedException);

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $exceptionEvent = new ExceptionEvent($kernel, $request, 1, $commandHandlerFailed);

        $this->handler->onKernelException($exceptionEvent);

        $response = $exceptionEvent->getResponse();
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('{"errorMessage":"User already exists"}', $response->getContent());
    }

    /**
     * @covers ::onKernelException
     */
    public function testOnKernelExceptionExitsIfPreviousExceptionIsNotDomainException()
    {
        $invalidArgumentException = new \InvalidArgumentException('Test message');

        $envelope = new Envelope(new \stdClass());
        $handlerFailedException = new HandlerFailedException($envelope, [$invalidArgumentException]);

        $commandHandlerFailed = new CommandHandlerFailed('', null, $handlerFailedException);

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $exceptionEvent = new ExceptionEvent($kernel, $request, 1, $commandHandlerFailed);

        $responseBefore = $exceptionEvent->getResponse();
        $this->handler->onKernelException($exceptionEvent);
        $this->assertSame($responseBefore, $exceptionEvent->getResponse());
    }

    /**
     * @covers ::onKernelException
     */
    public function testOnKernelExceptionExitsIfExceptionIsNotHandlerFailedException()
    {
        $invalidArgumentException = new \InvalidArgumentException();
        $commandHandlerFailed = new CommandHandlerFailed($invalidArgumentException);
        
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $exceptionEvent = new ExceptionEvent($kernel, $request, 1, $commandHandlerFailed);

        $responseBefore = $exceptionEvent->getResponse();
        $this->handler->onKernelException($exceptionEvent);
        $this->assertSame($responseBefore, $exceptionEvent->getResponse());
    }
}
