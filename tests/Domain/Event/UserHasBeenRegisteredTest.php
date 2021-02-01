<?php

namespace App\Tests\Domain\Event;

use App\Domain\Event\Event;
use App\Domain\Event\UserHasBeenRegistered;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Domain\Event\UserHasBeenRegistered
 */
class UserHasBeenRegisteredTest extends TestCase
{

    private UserHasBeenRegistered $event;

    protected function setUp(): void
    {
        $this->event = new UserHasBeenRegistered('5563b817-948a-466b-b3a1-a8d64518cd8f');
    }
    /**
     * @covers ::__construct
     */
    public function testConstruct(){
        $this->assertInstanceOf(Event::class, $this->event);
    }

    /**
     * @covers ::getUserId
     */
    public function testGetUserId()
    {
        $this->assertSame('5563b817-948a-466b-b3a1-a8d64518cd8f', $this->event->getUserId());
    }
}
