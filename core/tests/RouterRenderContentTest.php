<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterRenderContentTest extends testcase
{

    /** @test */
    public function it_can_render_a_controller_that_has_constructor_method_injection()
    {
        $router = new Router(new Container());
        $router->get('/about', [AboutController::class, 'index']);
        $content = $router->render('/about', 'GET');
        $this->assertEquals('about', $content);
    }

    /** @test */
    public function it_can_render_a_closure()
    {
        $router = new Router(new Container());
        $router->get('/about', fn() => 'closure');
        $content = $router->render('/about', 'GET');
        $this->assertEquals('closure', $content);
    }

    /** @test */
    public function it_can_render_a_controller_invoke_method_if_no_method_specify()
    {
        $router = new Router(new Container());
        $router->get('/about', AboutController::class);
        $content = $router->render('/about', 'GET');
        $this->assertEquals('about', $content);
    }

    /** @test */
    public function it_can_render_a_dynamic_route_and_pass_correct_argument_to_controller()
    {
        $this->markTestSkipped();

        $router = new Router(new Container());
        $router->get('/user/{id}/post/{post_id}', [UserController::class, 'show']);
        $content = $router->render('/user/1/post/23_56', 'GET');
//       $this->assertEquals(1,$content);
    }
}


class AboutController
{
    public function __construct(Request $request)
    {
        //
    }

    public function __invoke()
    {
        return 'about';
    }

    public function index(Foo $foo)
    {
        return 'about';
    }
}

class Foo
{

}

class UserController
{
    public function show($id)
    {
        return $id;
    }
}