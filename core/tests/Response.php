<?php

namespace Andileong\Framework\Core\tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response
{
    protected $body;
    protected $statusCode;

    public function __construct(protected testcase $testCase, protected SymfonyResponse $response)
    {
        $this->body = $this->response->getContent();
        $this->statusCode = $this->response->getStatusCode();
    }

    public function assertOk()
    {
        $this->testCase->assertTrue(
            $this->statusCode === \Symfony\Component\HttpFoundation\Response::HTTP_OK
        );
        return $this;
    }

    public function assertJson()
    {
        $this->testCase->assertJson($this->body);
        return $this;
    }

}