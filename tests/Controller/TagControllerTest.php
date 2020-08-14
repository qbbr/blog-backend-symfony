<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class TagControllerTest extends ApiTestCase
{
    public function testIndex()
    {
        $response = $this->request('GET', '/tags/');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = $this->getJsonContent($response);
        $this->assertIsArray($responseData);
        $array = $responseData[array_rand($responseData)];
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('postsCount', $array);
    }
}
