<?php

namespace App\Tests\Application\EventHandler;

use App\Application\EventHandler\EventHandler;
use App\Application\EventHandler\SendWelcomeMessage;
use App\Domain\Event\UserHasBeenRegistered;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\EventHandler\SendWelcomeMessage
 */
class SendWelcomeMessageTest extends TestCase
{

    private SendWelcomeMessage $handler;

    protected function setUp(): void
    {
        $this->handler = new SendWelcomeMessage();
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $this->assertInstanceOf(EventHandler::class, $this->handler);
        $userHasBeenRegistered = $this->createMock(UserHasBeenRegistered::class);
        $this->handler->__invoke($userHasBeenRegistered);
    }
}
