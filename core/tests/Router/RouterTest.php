<?php

namespace Andileong\Framework\Tests\Router;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    /** @test */
    public function it_can_register_get_method()
    {
        $router = app('router');
        $router->get('/user',fn() => 'user');

        $match = array_filter($router->routes['GET'],fn($route) => $route->matches('/user'));
        $this->assertCount(1,$match);
    }

    /** @test */
    public function it_can_register_post_method()
    {
        $router = app('router');
        $router->post('/user',fn() => 'user');

        $match = array_filter($router->routes['POST'],fn($route) => $route->matches('/user'));
        $this->assertCount(1,$match);
    }

    /** @test */
    public function it_can_register_delete_method()
    {
        $router = app('router');
        $router->delete('/user',fn() => 'user');

        $match = array_filter($router->routes['DELETE'],fn($route) => $route->matches('/user'));
        $this->assertCount(1,$match);
    }

    /** @test */
    public function it_can_register_put_method()
    {
        $router = app('router');
        $router->put('/user',fn() => 'user');

        $match = array_filter($router->routes['PUT'],fn($route) => $route->matches('/user'));
        $this->assertCount(1,$match);
    }

    /** @test */
    public function it_can_register_patch_method()
    {
        $router = app('router');
        $router->patch('/user',fn() => 'user');

        $match = array_filter($router->routes['PATCH'],fn($route) => $route->matches('/user'));
        $this->assertCount(1,$match);
    }
}
