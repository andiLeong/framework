<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends testcase
{

    /** @test */
    public function it_can_find_the_matching_route()
    {
        $route3 = new Route('/user/{id}/post/{post-id}', 'GET', '');
        $route2 = new Route('/user/{id}', 'GET', '');
        $route = new Route('/about', 'GET', '');


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

        $this->assertEquals(['user'],$route2->getStaticSegments());
        $this->assertEquals(['user','post'],$route3->getStaticSegments());
    }
}