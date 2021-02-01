<?php

namespace App\Tests\UI\Web\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @coversDefaultClass \App\UI\Web\Controller\UserController
 */
class UserControllerTest extends WebTestCase
{

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @covers ::__construct
     * @covers ::index
     */
    public function testIndex()
    {
        $this->login('test@user.com', 'zaq12wsx');

        $this->jsonRequest('GET', '/user');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($responseData['id']);
        $this->assertSame('test@user.com', $responseData['email']);
        $this->assertArrayNotHasKey('password', $responseData);
        $this->assertArrayNotHasKey('roles', $responseData);
    }

    /**
     * @covers ::register
     */
    public function testRegister()
    {
        $this->jsonRequest('POST', '/user', json_encode([
            'email' => 'another@user.com',
            'password' => 'zaq12wsx',
        ]));

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @covers ::register
     */
    public function testCannotRegisterTwice(){

        $this->jsonRequest('POST', '/user', json_encode([
            'email' => 'test@user.com',
            'password' => 'zaq12wsx',
        ]));

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame('User already exists', $responseData['errorMessage']);

    }
}
