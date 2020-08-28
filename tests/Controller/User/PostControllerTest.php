<?php

namespace App\Tests\Controller\User;

use App\Tests\Controller\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends ApiTestCase
{
    private $postData = [
        'title' => 'my post title 1',
        'text' => 'my post long text',
        'tags' => [
            ['name' => 'tag1'],
            ['name' => 'tag2'],
        ],
        'isPrivate' => false,
    ];

    private $newPostData = [
        'title' => 'new my post title 1',
        'text' => 'new my post long text',
        'tags' => [
            ['name' => 'newtag1'],
            ['name' => 'newtag2'],
        ],
        'isPrivate' => false,
    ];

    private static $postId;

    public function testCreate()
    {
        $response = $this->requestWithToken('POST', '/user/post/', $this->postData);
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = $this->getJsonContent($response);
        $this->assertArrayHasKey('id', $responseData);

        self::$postId = $responseData['id'];
    }

    public function testAll()
    {
        $response = $this->requestWithToken('GET', '/user/posts/');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = $this->getJsonContent($response);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('results', $responseData);
        $this->assertArrayHasKey('page', $responseData);
        $this->assertArrayHasKey('pageSize', $responseData);
        $this->assertArrayHasKey('total', $responseData);
        $results = $responseData['results'];
        $this->assertIsArray($results);
    }

    public function testId()
    {
        $response = $this->requestWithToken('GET', '/user/post/'.self::$postId.'/');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $postData = $this->getJsonContent($response);
        $this->assertSame($this->postData['title'], $postData['title']);
        $this->assertSame($this->postData['text'], $postData['text']);
        $this->assertSame($this->postData['tags'], $postData['tags']);
        $this->assertSame($this->postData['isPrivate'], $postData['isPrivate']);
        $this->assertArrayHasKey('html', $postData);
    }

    public function testUpdate()
    {
        $response = $this->requestWithToken('PUT', '/user/post/'.self::$postId.'/', $this->newPostData);
        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testIdNewData()
    {
        $response = $this->requestWithToken('GET', '/user/post/'.self::$postId.'/');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $postData = $this->getJsonContent($response);
        $this->assertSame($this->newPostData['title'], $postData['title']);
        $this->assertSame($this->newPostData['text'], $postData['text']);
        $this->assertSame($this->newPostData['tags'], $postData['tags']);
        $this->assertSame($this->newPostData['isPrivate'], $postData['isPrivate']);
        $this->assertArrayHasKey('html', $postData);
    }

    public function testDelete()
    {
        $response = $this->requestWithToken('DELETE', '/user/post/'.self::$postId.'/');
        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteAll()
    {
        $response = $this->requestWithToken('DELETE', '/user/posts/');
        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testMd2html()
    {
        $response = $this->requestWithToken('POST', '/user/post/md2html/', [
            'text' => 'asd',
        ]);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $data = $this->getJsonContent($response);
        $this->assertArrayHasKey('html', $data);
    }
}
