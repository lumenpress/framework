<?php

namespace LumenPress\Tests\Routing;

class RequestMethodTest extends TestCase
{
    protected function createApplication()
    {
        $app = parent::createApplication();

        $app['wp.router']->post('author', function () {
            return response('POST');
        });

        $app['wp.router']->put('author', function () {
            return response('PUT');
        });

        $app['wp.router']->patch('author', function () {
            return response('PATCH');
        });

        $app['wp.router']->delete('author', function () {
            return response('DELETE');
        });

        $app['wp.router']->options('author', function () {
            return response('OPTIONS');
        });

        return $app;
    }

    /**
     * @group method
     */
    public function testRoute1()
    {
        $app = $this->createApplication();

        $response = $this->callAuthorUrl($app, 1, 'GET');

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @group method
     */
    public function testRoute2()
    {
        $app = $this->createApplication();

        $response = $this->callAuthorUrl($app, 1, 'POST');

        $this->assertEquals(200, $response->getStatusCode());
        self::assertEquals('POST', $response->getContent());
    }

    /**
     * @group method
     */
    public function testRoute3()
    {
        $app = $this->createApplication();

        $response = $this->callAuthorUrl($app, 1, 'PUT');

        $this->assertEquals(200, $response->getStatusCode());
        self::assertEquals('PUT', $response->getContent());
    }

    /**
     * @group method
     */
    public function testRoute4()
    {
        $app = $this->createApplication();

        $response = $this->callAuthorUrl($app, 1, 'PATCH');

        $this->assertEquals(200, $response->getStatusCode());
        self::assertEquals('PATCH', $response->getContent());
    }

    /**
     * @group method
     */
    public function testRoute5()
    {
        $app = $this->createApplication();

        $response = $this->callAuthorUrl($app, 1, 'DELETE');

        $this->assertEquals(200, $response->getStatusCode());
        self::assertEquals('DELETE', $response->getContent());
    }

    /**
     * @group method
     */
    public function testRoute6()
    {
        $app = $this->createApplication();

        $response = $this->callAuthorUrl($app, 1, 'OPTIONS');

        $this->assertEquals(200, $response->getStatusCode());
        self::assertEquals('OPTIONS', $response->getContent());
    }
}
