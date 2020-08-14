<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends ApiTestCase
{
    public function testRegister()
    {
        // register
        $response = $this->request('POST', '/register/', $this->authData);
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = $this->getJsonContent($response);
        $this->assertArrayHasKey('token', $responseData);

        // register again for error of username already exists
        $response = $this->request('POST', '/register/', $this->authData);
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = $this->getJsonContent($response);
        $this->assertArrayHasKey('errors', $responseData);
    }

    public function testLogin()
    {
        $token = $this->getToken();
        $this->assertNotEmpty($token);
    }
}
