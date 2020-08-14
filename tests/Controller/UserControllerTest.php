<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends ApiTestCase
{
    public function testIndex()
    {
        $response = $this->requestWithToken('GET', '/private/user/');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = $this->getJsonContent($response);
        $this->assertSame($this->authData['username'], $responseData['username']);
    }

    public function testUpdate()
    {
        $data = ['about' => 'my self text'];
        $response = $this->requestWithToken('PUT', '/private/user/', $data);
        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

//    public function testDelete()
//    {
//        $response = $this->requestWithToken('DELETE', '/private/user/');
//        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
//    }
}
