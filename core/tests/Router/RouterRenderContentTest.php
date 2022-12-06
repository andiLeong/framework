<?php

namespace Andileong\Framework\Core\tests\Router;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Pipeline\Pipeline;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use Andileong\Framework\Core\tests\CreateUser;
use Andileong\Framework\Core\tests\Http;
use Andileong\Framework\Core\tests\Transaction;
use Mockery;
//use PHPUnit\Framework\TestCase;

class RouterRenderContentTest extends Mockery\Adapter\Phpunit\MockeryTestCase
{
    use Http;
    use CreateUser;
    use Transaction;

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
        $content = $router->render('/user/1/post/23_56',);
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


    /** @test */
    public function it_can_get_a_model_in_json_if_return_directly_from_route()
    {
        $user = $this->createUser();
        $response = $this->get("/user/{$user->id}",['foo' => 'va']);
        $response->assertJson()->assertOk();
    }

    /** @test */
    public function it_can_render_different_verb_of_routes()
    {
        $user = $this->createUser();
        $response = $this->post("/user/{$user->id}",['foo' => 'va']);
        $response->assertNotFound();
    }


    /** @test */
    public function it_can_render_middleware_route()
    {
        $this->markTestSkipped();
    }

    /** @test */
    public function pipeline_is_call_when_render_route()
    {
        $this->markTestSkipped();
        $mock = Mockery::mock(Pipeline::class);
        $mock->shouldReceive('run')->andReturn($mock)->once();
        $mock->shouldReceive('send')->andReturn($mock)->once();
        $mock->shouldReceive('then')->once();
        $mock->shouldReceive('through')->andReturn($mock)->once();
        $app = new Application($_SERVER['DOCUMENT_ROOT'],Request::setTest([], [], ['REQUEST_URI' => '/foo', 'REQUEST_METHOD' => 'GET']));
        $app->bind('mock',$mock);
//        dd(app());
//        $mock->run();

        $router = new Router($app);
        $router->middleware('foo')->get('/foo', fn() => 'foo');
        $content = $router->render();

//        $pipeline = (new Pipeline)->through([])->send('hi')->run();
//        $pipeline = (new Pipeline)->through([])->send('hi')->run();
//        dd($mock);
    }

    /**
     * @param $uri
     * @param $method
     * @return Router
     * @throws \ErrorException
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