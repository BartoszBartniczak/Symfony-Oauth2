<?php

namespace App\Tests\Application\EventHandler;

use App\Application\EventHandler\EventHandler;
use App\Application\EventHandler\SendNewUserNotification;
use App\Domain\Event\UserHasBeenRegistered;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\EventHandler\SendNewUserNotification
 */
class SendNewUserNotificationTest extends TestCase
{

    private SendNewUserNotification $handler;

    protected function setUp(): void
    {
        $this->handler = new SendNewUserNotification();
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
