<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterRenderContentTest extends testcase
{
    private Router $router;

    public function setUp() :void
    {
        $this->router = new Router(new Application($_SERVER['DOCUMENT_ROOT']));
    }

    /** @test */
    public function it_can_render_a_controller_that_has_constructor_method_injection()
    {
        $this->router->get('/about', [AboutController::class, 'index']);
        $content = $this->router->render('/about', 'GET');
        $this->assertEquals('about', $content);
    }

    /** @test */
    public function it_can_render_a_closure()
    {
        $this->router->get('/about', fn() => 'closure');
        $content = $this->router->render('/about', 'GET');
        $this->assertEquals('closure', $content);
    }

    /** @test */
    public function it_can_render_a_controller_invoke_method_if_no_method_specify()
    {
        $this->router->get('/about', AboutController::class);
        $content = $this->router->render('/about', 'GET');
        $this->assertEquals('about', $content);
    }

    /** @test */
    public function it_can_render_a_dynamic_route_and_pass_correct_argument_to_controller()
    {
        $this->router->get('/user/{id}/post/{post_id}', [UserController::class, 'show']);
        $content = $this->router->render('/user/1/post/23_56', 'GET');
        $this->assertEquals(['1', '23_56'], $content);

        $this->router->get('/user/{id}', [UserController::class, 'edit']);
        $content = $this->router->render('/user/1', 'GET');
        $this->assertEquals('1', $content);

        $this->router->get('/user/{id}', [UserController::class, 'index']);
        $content = $this->router->render('/user/1', 'GET');
        $this->assertEquals('1', $content);

        $this->router->get('/foo', [Foo::class, 'index']);
        $content = $this->router->render('/foo', 'GET');
        $this->assertEquals('foo', $content);
    }

    /** @test */
    public function it_can_render_a_dynamic_route_and_pass_correct_argument_to_closure()
    {
        $this->router->get('/user/{id}/post/{post_id}', fn($id, $post_id, Foo $foo) => [$id, $post_id]);
        $content = $this->router->render('/user/1/post/23_56', 'GET');
        $this->assertEquals(['1', '23_56'], $content);

        $this->router->get('/user/{id}', fn($id, Request $request) => $id);
        $content = $this->router->render('/user/1', 'GET');
        $this->assertEquals('1', $content);

        $this->router->get('/user', fn(Request $request) => 1);
        $content = $this->router->render('/user', 'GET');
        $this->assertEquals('1', $content);
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

    public function index()
    {
        return 'foo';
    }
}

class UserController
{
    public function show($id, $post_id)
    {
        return [$id, $post_id];
    }

    public function edit($id)
    {
        return $id;
    }

    public function index(Request $request, $id)
    {
        return $id;
    }
}