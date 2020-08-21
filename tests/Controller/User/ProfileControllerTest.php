<?php

namespace App\Tests\Controller\User;

use App\Tests\Controller\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProfileControllerTest extends ApiTestCase
{
    public function testIndex()
    {
        $response = $this->requestWithToken('GET', '/user/profile/');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = $this->getJsonContent($response);
        $this->assertSame($this->authData['username'], $responseData['username']);
        $this->assertArrayHasKey('postsCount', $responseData);
    }

    public function testUpdate()
    {
        $data = ['about' => 'my self text'];
        $response = $this->requestWithToken('PUT', '/user/profile/', $data);
        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

//    public function testDelete()
//    {
//        $response = $this->requestWithToken('DELETE', '/user/profile/');
//        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
//    }
}
