<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiTestCase extends WebTestCase
{
    private $client;
    private $token;

    protected $authData = [
        'username' => 'testuser1',
        'password' => 'testpassword1',
    ];

    protected function setUp()
    {
        if (null === $this->client) {
            $this->client = static::createClient();
        }
    }

    protected function getToken(): string
    {
        if (empty($this->token)) {
            $response = $this->request('POST', '/login/', $this->authData);
            $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
            $this->assertJson($response->getContent());
            $responseData = $this->getJsonContent($response);
            $this->assertArrayHasKey('token', $responseData);
            $this->token = $responseData['token'];
        }

        return $this->token;
    }

    protected function request(string $method, string $uri, array $data = null, string $token = null): Response
    {
        $server = ['CONTENT_TYPE' => 'application/json'];
        if (null !== $token) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer '.$token;
        }
        $content = null === $data ? null : json_encode($data);
        $this->client->request($method, $uri, [], [], $server, $content);

        return $this->client->getResponse();
    }

    protected function requestWithToken(string $method, string $uri, array $data = null): Response
    {
        $token = $this->getToken();

        return $this->request($method, $uri, $data, $token);
    }

    protected function getJsonContent(Response $response): array
    {
        return json_decode($response->getContent(), true);
    }
}
