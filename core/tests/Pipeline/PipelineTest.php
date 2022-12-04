<?php

namespace Andileong\Framework\Tests\Pipeline;

use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;

class PipelineTest extends TestCase
{
    private array $object;

    public function setUp() :void
    {
        $this->object = ['foo' => 'bar'];
    }

    /** @test */
    public function each_pipe_can_mutate_the_object()
    {
        $pipeline = $this->pipeline($this->object, [
            new PipeOne(),
            new PipeTwo(),
        ]);

        $result = $pipeline->run()->result();

        $this->assertEquals([
            'foo' => 'bar',
            'a' => 'b',
            'c' => 'd',
        ], $result);
    }

    /** @test */
    public function it_can_pass_class_let_pipeline_to_instantiate()
    {
        $pipeline = $this->pipeline($this->object, [
            PipeOne::class,
            PipeTwo::class,
        ]);

        $result = $pipeline->run()->result();

        $this->assertEquals([
            'foo' => 'bar',
            'a' => 'b',
            'c' => 'd',
        ], $result);
    }

    /** @test */
    public function each_pipe_has_the_opportunity_to_stop_the_pipe()
    {
        $pipeline = $this->pipeline($this->object, [
            PipeOne::class,
            PipeThree::class,
            PipeTwo::class,
        ]);

        $result = $pipeline->run()->result();
        $this->assertEquals('stop', $result);
    }

    /** @test */
    public function each_pipe_can_be_a_closure()
    {
        $pipeline = $this->pipeline($this->object, [
            function ($obj, $instance) {
                $obj['a'] = 'b';
                return $instance->next($obj);
            },
            function ($obj, $instance) {
                $obj['c'] = 'd';
                return $instance->next($obj);
            },
        ]);

        $result = $pipeline->run()->result();

        $this->assertEquals([
            'foo' => 'bar',
            'a' => 'b',
            'c' => 'd',
        ], $result);
    }

    /** @test */
    public function closure_pipe_can_stop_the_pipes_as_well()
    {
        $pipeline = $this->pipeline($this->object, [
            function ($obj, $instance) {
                $obj['a'] = 'b';
                return $instance->next($obj);
            },
            function ($obj, $instance) {
                $instance->break('stop');
                $obj['c'] = 'd';
                return $instance->next($obj);
            },
        ]);

        $result = $pipeline->run()->result();

        $this->assertEquals('stop', $result);
    }

    public function pipeline($object, $pipes)
    {
        return (new Pipeline(app()))
            ->send($object)
            ->through($pipes);
    }
}

class PipeOne extends Chainable
{
    public function handle($object)
    {
        $object['a'] = 'b';
        return $this->next($object);
    }
}

class PipeTwo extends Chainable
{
    public function handle($object)
    {
        $object['c'] = 'd';
        return $this->next($object);
    }
}

class PipeThree extends Chainable
{
    public function handle($object)
    {
        $this->break('stop');
        return $this->next($object);
    }
}