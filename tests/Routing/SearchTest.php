<?php

namespace LumenPress\Tests\Routing;

class SearchTest extends TestCase
{
    public function testRoute()
    {
        $this->setPermalinkStructure('/%year%/%monthnum%/%day%/%postname%/');

        $app = $this->createApplication();

        $app['wp.router']->is('search', function () {
            return response('Hello World');
        });

        $response = $this->call($app, '/?s=abc');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }
}
