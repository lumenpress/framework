<?php

namespace LumenPress\Tests\Routing;

class GroupTest extends TestCase
{
    /**
     * @group group
     */
    public function testGroup()
    {
        $this->setPermalinkStructure('/%year%/%monthnum%/%day%/%postname%/');

        $app = $this->createApplication();

        $app['wp.router']->group([
            'namespace' => 'LumenPress\Tests\Routing\Controllers', ], function ($router) {
                $router->is('home', 'TestController@home');
            });

        $response = $this->call($app, '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group group
     */
    public function testGroup2()
    {
        $app = $this->createApplication();

        $app->routeMiddleware([
            'auth' => Authenticate::class,
        ]);

        $app['wp.router']->group([
            'namespace' => 'LumenPress\Tests\Routing\Controllers',
            'middleware' => 'auth', ], function ($router) {
                $router->is('home', 'TestController@home');
            });

        $response = $this->call($app, '/');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getContent());

        $response = $this->call($app, '/?auth=1');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group group
     */
    public function testGroup3()
    {
        $app = $this->createApplication();

        $app->routeMiddleware([
            'auth' => Authenticate::class,
        ]);

        $app['wp.router']->is('home', [
            'middleware' => 'auth',
            'uses' => 'LumenPress\Tests\Routing\Controllers\TestController@home',
        ]);

        $response = $this->call($app, '/');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getContent());

        $response = $this->call($app, '/?auth=1');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }
}

class Authenticate
{
    public function handle($request, \Closure $next)
    {
        return $request->input('auth') ? $next($request) : response('Not Found', 404);
    }
}
