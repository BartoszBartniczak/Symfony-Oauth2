<?php


namespace App\Tests\UI\Web\Controller;


use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

abstract class WebTestCase extends SymfonyWebTestCase
{

    protected KernelBrowser $client;
    private string $accessToken = '';

    protected function setUp(): void
    {
        $this->client = static::createClient([
            'environment' => 'test',
        ]);
    }

    protected function login(string $email, string $password): void
    {
        $this->client->request('POST',
            '/token',
            ['grant_type' => 'password',
                'client_id' => 'react-client-app',
                'username' => $email,
                'password' => $password
            ],
        );

        if ($this->client->getResponse()->getStatusCode() !== Response::HTTP_OK) {
            throw new \Exception('Cannot log in.');
        }

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->accessToken = $responseData['access_token'];
    }


    protected function jsonRequest(string $method, string $uri, ?string $jsonBody = null): ?Crawler
    {
        $server['CONTENT_TYPE'] = 'application/json';

        if ($this->isLoggedIn()) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer ' . $this->accessToken;
        }


        return $this->client->request($method, $uri, [], [], $server, $jsonBody);
    }

    private function isLoggedIn(): bool
    {
        return $this->accessToken !== '';
    }


}
