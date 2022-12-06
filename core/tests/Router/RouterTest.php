<?php

namespace Andileong\Framework\Tests\Router;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    /** @test */
    public function it_can_register_get_method()
    {
        $router = app('router');
        $router->get('/user', fn() => 'user');

        $match = array_filter($router->routes['GET'], fn($route) => $route->matches('/user'));
        $this->assertCount(1, $match);
    }

    /** @test */
    public function it_can_register_post_method()
    {
        $router = app('router');
        $router->post('/user', fn() => 'user');

        $match = array_filter($router->routes['POST'], fn($route) => $route->matches('/user'));
        $this->assertCount(1, $match);
    }

    /** @test */
    public function it_can_register_delete_method()
    {
        $router = app('router');
        $router->delete('/user', fn() => 'user');

        $match = array_filter($router->routes['DELETE'], fn($route) => $route->matches('/user'));
        $this->assertCount(1, $match);
    }

    /** @test */
    public function it_can_register_put_method()
    {
        $router = app('router');
        $router->put('/user', fn() => 'user');

        $match = array_filter($router->routes['PUT'], fn($route) => $route->matches('/user'));
        $this->assertCount(1, $match);
    }

    /** @test */
    public function it_can_register_patch_method()
    {
        $router = app('router');
        $router->patch('/user', fn() => 'user');

        $match = array_filter($router->routes['PATCH'], fn($route) => $route->matches('/user'));
        $this->assertCount(1, $match);
    }

    /** @test */
    public function it_can_add_middleware_to_a_route()
    {
        $router = app('router');
        $route = $router->middleware(['foo', 'bar'])->get('/user', fn() => 'user');
        $route2 = $router->get('/user2', fn() => 'user')->middleware('baz');

        $this->assertEquals(['foo', 'bar'], $route->getRegisteredMiddleware());
        $this->assertEquals(['baz'], $route2->getRegisteredMiddleware());
    }

    /** @test */
    public function it_can_add_middleware_to_routes_from_group()
    {
        $router = app('router');

        $route4 = $router->middleware('baz')->get('/user4', fn() => 'user');
        $this->assertEquals(['baz'], $route4->getRegisteredMiddleware());

        $router->middleware('foo','bar')->group(function($router){
            $route = $router->get('/user', fn() => 'user');
            $route2 = $router->get('/user2', fn() => 'user');

            $this->assertEquals(['foo', 'bar'], $route->getRegisteredMiddleware());
            $this->assertEquals(['foo', 'bar'], $route2->getRegisteredMiddleware());
        });


        $route3 = $router->get('/user3', fn() => 'user');
        $this->assertEquals([], $route3->getRegisteredMiddleware());
    }
}
