<?php

use Illuminate\Events\Dispatcher;
use CyberDuck\RestRouter\Router;

class RouterTest extends PHPUnit_Framework_TestCase {

    protected $router;

    public function setUp()
    {
        $this->router = new Router(new Dispatcher);
    }

    public function testRestRouting()
    {
        $this->router->rest('foo', 'FooController', ['model' => 'FooModel']);
        $routes = $this->router->getRoutes();
        $this->assertEquals(7, count($routes));
    }

    public function testRestRoutingOnly()
    {
        $options = [
            'model' => 'FooModel',
            'only' => 'index'
        ];
        $this->router->rest('foo', 'FooController', $options);
        $routes = $this->router->getRoutes();
        $this->assertEquals(2, count($routes));
    }

    public function testRestRoutingExcept()
    {
        $options = [
            'model' => 'FooModel',
            'except' => 'index'
        ];
        $this->router->rest('foo', 'FooController', $options);
        $routes = $this->router->getRoutes();
        $this->assertEquals(6, count($routes));
    }

    public function testRestRoutingNoModel()
    {
        $this->router->rest('foo', 'FooController', ['except' => ['index', 'store']]);
        $routes = $this->router->getRoutes();
        $routes = $routes->getRoutes();
        $this->assertEquals('foo/{id?}/{_path?}', $routes[0]->getUri());
    }
}

class FooController {}
