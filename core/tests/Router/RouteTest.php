<?php

namespace Andileong\Framework\Core\tests\Router;

use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends testcase
{

    /** @test */
    public function it_can_find_the_matching_route()
    {
        $route3 = new Route('/user/{id}/post/{post-id}', 'GET', '');
        $route2 = new Route('/user/{id}', 'GET', '');
        $route4 = new Route('/user/{id}', 'GET', '');
        $route = new Route('/about', 'GET', '');


        $this->assertTrue($route4->matches('/user/1000'));
        $this->assertTrue($route3->matches('/user/1/post/3'));
        $this->assertTrue($route2->matches('/user/1'));
        $this->assertFalse($route2->matches('/user/1/post/3'));
        $this->assertFalse($route2->matches('/user/1/sm'));
        $this->assertTrue($route->matches('/about'));
        $this->assertTrue($route3->matches('/user/1/post/3'));
        $this->assertFalse($route3->matches('/user/1/post/3/julie'));
        $this->assertFalse($route3->matches('hello/user/1/post/3/julie'));
    }

    /** @test */
    public function it_can_parse_dynamic_uri()
    {
        $route = new Route('/about', 'GET', '');
        $route2 = new Route('/user/{id}', 'GET', '');
        $route3 = new Route('/user/{id}/post/{postId}', 'GET', '');
        $this->assertTrue($route2->isDynamic());
        $this->assertTrue($route3->isDynamic());
        $this->assertFalse($route->isDynamic());
    }

    /** @test */
    public function it_can_record_static_segments()
    {
        $route2 = new Route('/user/{id}', 'GET', '');
        $route3 = new Route('/user/{id}/post/{postId}', 'GET', '');

        $this->assertEquals(['user'], $route2->getStaticSegments());
        $this->assertEquals(['user','post'], $route3->getStaticSegments());
    }

    /** @test */
    public function it_can_add_middlewares_to_route()
    {
        $route2 = new Route('foo', 'GET', new container());
        $this->assertEmpty($route2->getMiddleware());
        $route2->middleware(['foo']);
        $this->assertEquals(['foo'], $route2->getRegisteredMiddleware());

        $route3 = new Route('foo', 'GET', new Container);
        $route3->middleware(['foo']);
        $route3->middleware(['bar']);
        $this->assertEquals(['foo','bar'], $route3->getRegisteredMiddleware());
    }
}
