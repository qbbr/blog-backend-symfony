<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends ApiTestCase
{
    private static $postSlug = '';

    public function testIndex()
    {
        $response = $this->request('GET', '/posts/');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = $this->getJsonContent($response);
        $this->assertArrayHasKey('results', $responseData);
        $this->assertArrayHasKey('page', $responseData);
        $this->assertArrayHasKey('count', $responseData);
        $this->assertArrayHasKey('pageSize', $responseData);
        $this->assertArrayHasKey('total', $responseData);
        $results = $responseData['results'];
        $this->assertIsArray($results);
        $postData = $results[array_rand($results)];
        $this->assertPostHasProps($postData);
        self::$postSlug = $postData['slug'];
    }

    public function testGetBySlug()
    {
        $response = $this->request('GET', '/post/'.self::$postSlug.'/');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = $this->getJsonContent($response);
        $this->assertPostHasProps($responseData);
    }

    private function assertPostHasProps(array $postData)
    {
        $this->assertArrayHasKey('id', $postData);
        $this->assertArrayHasKey('user', $postData);
        $this->assertArrayHasKey('title', $postData);
        $this->assertArrayHasKey('slug', $postData);
        $this->assertArrayHasKey('text', $postData);
        $this->assertArrayHasKey('tags', $postData);
        $this->assertArrayHasKey('isPrivate', $postData);
        $this->assertArrayHasKey('createdAt', $postData);
        $this->assertArrayHasKey('updatedAt', $postData);
    }
}
