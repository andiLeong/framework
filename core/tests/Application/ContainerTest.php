<?php

namespace Andileong\Framework\Tests;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Container\Exception\InstantiateException;
use Andileong\Framework\Core\Request\Request;
use PHPUnit\Framework\TestCase;

class ContainerTest extends testCase
{
    /** @test */
    public function it_can_bind_value()
    {
        $container = new Container();
        $container->bind('foo', 'bar');
        $this->assertEquals('bar', $container->get('foo'));
    }

    /** @test */
    public function it_can_resolve_closure()
    {
        $container = new Container();
        $container->bind('foo', function () {
            return new NoDependency();
        });

        $this->assertInstanceOf(NoDependency::class, $container->get('foo'));
    }

    /** @test */
    public function it_can_resolve_singleton()
    {
        $container = new Container();
        $container->singleton('foo', function () {
            return new NoDependency();
        });

        $foo1 = $container->get('foo');
        $foo2 = $container->get('foo');
        $this->assertSame($foo1, $foo2);
    }

    /** @test */
    public function it_will_return_back_object_if_the_key_an_object()
    {
        $object = new NoDependency();
        $container = new Container();
        $result = $container->get($object);
        $this->assertSame($object,$result);
    }

    /** @test */
    public function it_can_instantiate_class_without_dependency_if_key_is_class_path()
    {
        $container = new Container();
        $this->assertInstanceOf(NoDependency::class, $container->get(NoDependency::class));
    }

    /** @test */
    public function it_can_instantiate_class_with_dependency()
    {
        $container = new Container();
        $this->assertInstanceOf(HasDependency::class, $container->get(HasDependency::class));
    }

    /** @test */
    public function it_can_not_instantiate_class_dependency_has_not_type_hint()
    {
        $this->expectException(InstantiateException::class);
        $container = new Container();
        $container->get(NoTypeHint::class);
    }

    /** @test */
    public function it_can_not_instantiate_class_dependency_contains_union_types()
    {
        $this->expectException(InstantiateException::class);
        $container = new Container();
        $container->get(UnionTypeDependency::class);
    }

    /** @test */
    public function it_can_not_instantiate_class_dependency_if_its_abstract_or_if_class_not_exist()
    {
        $this->expectException(InstantiateException::class);
        $container = new Container();
        $container->get(DependsOnAbstract::class);
    }

    /** @test */
    public function it_can_set_the_singleton_instance_directly()
    {
        $container = new Container();
        $this->assertEmpty($container->getSingleton());
        $container->setSingleton(NoDependency::class,new NoDependency());
        $this->assertNotEmpty($container->getSingleton());
        $this->assertArrayHasKey(NoDependency::class,$container->getSingleton());
    }

    /** @test */
    public function it_can_get_thing_out_of_singleton_even_not_a_singleton_binding()
    {
        $container = new Container();
        $instance = new NoDependency();
        $container->setSingleton(NoDependency::class,$instance);

        $this->assertSame($instance,$container->get(NoDependency::class));
    }

    /** @test */
    public function it_can_set_the_singleton_instance_directly_by_its_alias_if_any()
    {
        $container = new Application($_SERVER['DOCUMENT_ROOT']);
        $container->setSingleton(Request::class,new Request());
        $this->assertArrayHasKey('request',$container->getSingleton());
        $this->assertArrayNotHasKey(Request::class,$container->getSingleton());
    }

    /** @test */
    public function it_can_access_container_as_array()
    {
        $container = new Container();
        $container->bind('foo','bar');
        $this->assertEquals('bar',$container['foo']);
    }

    /** @test */
    public function it_can_unset_container_key()
    {
        $container = new Container();
        $container->bind('foo','bar');
        $this->assertTrue($container->has('foo'));
        unset($container['foo']);
        $this->assertFalse($container->has('foo'));
    }

    /** @test */
    public function it_can_check_container_key_exist_like_array()
    {
        $container = new Container();
        $res = isset($container['foo']);
        $this->assertFalse($res);
    }

    /** @test */
    public function it_can_set_key_on_container_like_array()
    {
        $container = new Container();
        $container['foo'] = 'bar';
        $this->assertEquals('bar',$container['foo']);
    }
}


class NoDependency
{
}


class HasDependency
{
    public function __construct(public Foo $foo)
    {
    }
}

class Foo
{
    public function __construct(public Bar $bar)
    {
    }
}

class Bar
{
    public function __construct()
    {
    }
}

class NoTypeHint
{
    public function __construct($id)
    {
    }
}

class UnionTypeDependency
{
    public function __construct(Bar|Foo $id)
    {
    }
}

Abstract class AbstractDependency
{
}

class DependsOnAbstract
{
    public function __construct(AbstractDependency $a)
    {
    }
}