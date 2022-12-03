<?php

namespace Andileong\Framework\Tests\Exception;

use Andileong\Framework\Core\Exception\Handler;
use Andileong\Framework\Core\Exception\Renderer;
use Andileong\Framework\Core\Routing\RouteNotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExceptionHandlerTest extends TestCase
{

    /** @test */
    public function it_can_register_custom_exception_and_render_it()
    {
        $handler = $this->handler($this->exception());
        $this->assertInstanceOf(Response::class, $handler->handle());
        $this->assertEquals(json_encode(['message' => 'dummy exception']), $handler->handle()->getContent());
    }

    /** @test */
    public function it_can_render_internal_exceptions_accordingly()
    {
        $handler = $this->handler($this->exception(new RouteNotFoundException('route not found', '404')));

        $this->assertEquals(json_encode([
            'message' => 'route not found',
            'code' => 404
        ]), $handler->handle()->getContent());
    }

    /** @test */
    public function it_can_render_default_exceptions()
    {
        $handler = $this->handler($this->exception(new \InvalidArgumentException('invalid')));
        $response = json_decode($handler->handle()->getContent())->message;
        $this->assertEquals('invalid', $response);
    }

    public function exception($exception = null)
    {
        return $exception ?? new ExceptionStub('dummy exception');
    }

    public function handler($exception)
    {
        $renderer = new Renderer(app(),$exception);
        return new FakeAppExceptionHandler($exception,$renderer);
    }
}


class FakeAppExceptionHandler extends Handler
{
    public function register()
    {
        $this->customExceptions[ExceptionStub::class] = fn($e) => new JsonResponse(['message' => $e->getMessage()]);
    }

}

class ExceptionStub extends \Exception
{

}