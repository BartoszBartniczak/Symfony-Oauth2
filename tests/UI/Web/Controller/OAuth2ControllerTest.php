<?php


namespace App\Tests\UI\Web\Controller;


use Symfony\Component\HttpFoundation\Response;

class OAuth2ControllerTest extends WebTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGetToken()
    {
        
        $this->client->request('POST',
            '/token',
            ['grant_type' => 'password',
                'client_id' => 'react-client-app',
                'username' => 'test@user.com',
                'password' => 'zaq12wsx'],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('token_type', $responseData);
        $this->assertArrayHasKey('expires_in', $responseData);
        $this->assertArrayHasKey('access_token', $responseData);
        $this->assertArrayHasKey('refresh_token', $responseData);

        $this->assertSame('Bearer', $responseData['token_type']);
        $this->assertSame(3600, $responseData['expires_in']);
        $this->assertMatchesRegularExpression('/e(y|w)[^.]+\.e(y|w)[^.]+\.[^.]+/', $responseData['access_token']);
        $this->assertMatchesRegularExpression('/[a-z0-9]{36,}/', $responseData['refresh_token']);

    }

}
