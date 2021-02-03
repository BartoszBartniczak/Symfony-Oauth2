<?php

namespace App\Tests\Infrastructure\Symfony\EventListener;

use App\Infrastructure\Symfony\EventListener\UserResolveListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;
use Trikoder\Bundle\OAuth2Bundle\Model\Grant;

/**
 * @coversDefaultClass \App\Infrastructure\Symfony\EventListener\UserResolveListener
 */
class UserResolveListenerTest extends TestCase
{

    private UserResolveListener $listener;
    private UserProviderInterface|MockObject $userProvider;
    private UserPasswordEncoderInterface|MockObject $userPasswordEncoder;

    protected function setUp(): void
    {
        $this->userProvider = $this->createMock(UserProviderInterface::class);
        $this->userPasswordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->listener = new UserResolveListener($this->userProvider, $this->userPasswordEncoder);
    }
    

    /**
     * @covers ::onUserResolve
     * @covers ::__construct
     */
    public function testOnUserResolve()
    {
        $grant =$this->createMock(Grant::class);
        $client = $this->createMock(Client::class);
        $event = new UserResolveEvent('test@user.com', 'secret', $grant, $client);
        
        $user = $this->createMock(UserInterface::class);
        
        $this->userProvider->method('loadUserByUsername')
            ->with('test@user.com')
            ->willReturn($user);
        
        $this->userPasswordEncoder->method('isPasswordValid')->willReturn(true);
        
        $this->listener->onUserResolve($event);
        $this->assertSame($user, $event->getUser());
    }

    /**
     * @covers ::onUserResolve
     * @covers ::__construct
     */
    public function testOnUserResolveExitsIfPasswordIsIncorrect()
    {
        $grant =$this->createMock(Grant::class);
        $client = $this->createMock(Client::class);
        $event = new UserResolveEvent('test@user.com', 'secret', $grant, $client);

        $user = $this->createMock(UserInterface::class);

        $this->userProvider->method('loadUserByUsername')
            ->with('test@user.com')
            ->willReturn($user);

        $this->userPasswordEncoder->method('isPasswordValid')->willReturn(false);

        $this->listener->onUserResolve($event);
        $this->assertNull($event->getUser());
    }

    /**
     * @covers ::onUserResolve
     * @covers ::__construct
     */
    public function testOnUserResolveExitsIfCannotFindUser()
    {
        $grant =$this->createMock(Grant::class);
        $client = $this->createMock(Client::class);
        $event = new UserResolveEvent('test@user.com', 'secret', $grant, $client);

        $this->userProvider->method('loadUserByUsername')
            ->with('test@user.com')
            ->willThrowException($this->createMock(UsernameNotFoundException::class));

        $this->userPasswordEncoder->method('isPasswordValid')->willReturn(false);

        $this->listener->onUserResolve($event);
        $this->assertNull($event->getUser());
    }
}
