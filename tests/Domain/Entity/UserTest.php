<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @coversDefaultClass \App\Domain\Entity\User
 */
class UserTest extends TestCase
{

    private User $user;

    protected function setUp(): void
    {
        $id = '889628e3-0c59-48c7-a013-353c60299132';
        $email = 'test@email.com';
        $password = 'secret';
        $roles = ['ROLE_TEST'];
        $this->user = new User($id, $email, $password, $roles);
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(UserInterface::class, $this->user);
    }


    /**
     * @covers ::getId
     */
    public function testGetId()
    {
        $this->assertSame('889628e3-0c59-48c7-a013-353c60299132', $this->user->getId());
    }

    /**
     * @covers ::getRoles
     */
    public function testGetRoles()
    {
        $this->assertSame(['ROLE_TEST'], $this->user->getRoles());
    }

    /**
     * @covers ::changeRoles
     */
    public function testChangeRoles()
    {
        $this->user->changeRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $this->user->getRoles());
    }

    /**
     * @covers ::changeRoles
     * @covers ::getRoles
     */
    public function testChangeRolesSetsUniqueValues(){
        $this->user->changeRoles(['ROLE_USER', 'ROLE_USER']);
        $this->assertSame(['ROLE_USER'], $this->user->getRoles());
    }

    /**
     * @covers ::__construct
     * @covers ::getRoles
     */
    public function testConstructorSetsUniqueRoles(){
        $user = new User('f7e3c927-db79-418a-b5dd-0b57fed9a15e', 'another@test.com', 'secure', ['ROLE_USER', 'ROLE_USER']);
        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }

    /**
     * @covers ::getEmail
     */
    public function testGetEmail()
    {
        $this->assertSame('test@email.com', $this->user->getEmail());
    }

    /**
     * @covers ::getPassword
     */
    public function testGetPassword()
    {
        $this->assertSame('secret', $this->user->getPassword());
    }

    /**
     * @covers ::changePassword
     */
    public function testChangePassword()
    {
        $this->user->changePassword('myPass');
        $this->assertSame('myPass', $this->user->getPassword());
    }

    /**
     * @covers ::changeEmail
     */
    public function testChangeEmail()
    {
        $this->user->changeEmail('newemail@user.com');
        $this->assertSame('newemail@user.com', $this->user->getEmail());
    }

    /**
     * @covers ::getSalt
     */
    public function testGetSalt()
    {
        $this->assertNull($this->user->getSalt());
    }

    /**
     * @covers ::eraseCredentials
     */
    public function testEraseCredentials()
    {
        $this->assertNull($this->user->eraseCredentials());
    }

    /**
     * @covers ::getUsername
     */
    public function testGetUsername()
    {
        $this->assertSame('test@email.com', $this->user->getUsername());
    }
}
