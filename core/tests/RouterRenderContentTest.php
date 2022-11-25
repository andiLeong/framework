<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterRenderContentTest extends testcase
{
    /** @test */
    public function it_can_render_a_controller_that_has_constructor_method_injection()
    {
        $router = $this->getRouter('/about');
        $router->get('/about', [AboutController::class, 'index']);
        $content = $router->render();
        $this->assertEquals('about', $content);
    }

    /** @test */
    public function it_can_render_a_closure()
    {
        $router = $this->getRouter('/about');
        $router->get('/about', fn() => 'closure');
        $content = $router->render();
        $this->assertEquals('closure', $content);
    }

    /** @test */
    public function it_can_render_a_controller_invoke_method_if_no_method_specify()
    {
        $router = $this->getRouter('/about');
        $router->get('/about', AboutController::class);
        $content = $router->render();
        $this->assertEquals('about', $content);
    }

    /** @test */
    public function it_can_render_a_dynamic_route_and_pass_correct_argument_to_controller()
    {
        $router = $this->getRouter('/user/1/post/23_56');
        $router->get('/user/{id}/post/{post_id}', [UserController::class, 'show']);
        $content = $router->render();
        $this->assertEquals(['1', '23_56'], $content);

        $router = $this->getRouter('/user/1');
        $router->get('/user/{id}', [UserController::class, 'edit']);
        $content = $router->render();
        $this->assertEquals('1', $content);

        $router = $this->getRouter('/user/1');
        $router->get('/user/{id}', [UserController::class, 'index']);
        $content = $router->render();
        $this->assertEquals('1', $content);

        $router = $this->getRouter('/foo');
        $router->get('/foo', [Foo::class, 'index']);
        $content = $router->render('/foo', 'GET');
        $this->assertEquals('foo', $content);
    }

    /** @test */
    public function it_can_render_a_dynamic_route_and_pass_correct_argument_to_closure()
    {
        $router = $this->getRouter('/user/1/post/23_56');
        $router->get('/user/{id}/post/{post_id}', fn($id, $post_id, Foo $foo) => [$id, $post_id]);
        $content = $router->render('/user/1/post/23_56', );
        $this->assertEquals(['1', '23_56'], $content);

        $router = $this->getRouter('/user/1');
        $router->get('/user/{id}', fn($id, Request $request) => $id);
        $content = $router->render();
        $this->assertEquals('1', $content);

        $router = $this->getRouter('/user');
        $router->get('/user', fn(Request $request) => 1);
        $content = $router->render();
        $this->assertEquals('1', $content);
    }

    /**
     * @param $uri
     * @param $method
     * @return Router
     */
    protected function getRouter($uri, $method = 'GET'): Router
    {
        return new Router(new Application($_SERVER['DOCUMENT_ROOT'],
            Request::setTest([], [], ['REQUEST_URI' => $uri, 'REQUEST_METHOD' => $method])
        ));
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