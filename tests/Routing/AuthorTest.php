<?php

namespace LumenPress\Tests\Routing;

use LumenPress\Routing\Exceptions\RouteConditionException;

class AuthorTest extends TestCase
{
    /**
     * @group author
     */
    public function testRoute()
    {
        $this->setPermalinkStructure('/%year%/%monthnum%/%day%/%postname%/');

        $app = $this->createApplication();

        $app['wp.router']->is('author', function () {
            return response('Hello World');
        });

        $response = $this->callAuthorUrl($app, 1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group author
     */
    public function testRoute2()
    {
        $app = $this->createApplication();

        $app['wp.router']->is(['author' => 1], function () {
            return response('Hello World');
        });

        $response = $this->callAuthorUrl($app, 1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group author
     */
    public function testRoute3()
    {
        $app = $this->createApplication();

        $app['wp.router']->is(['author' => get_userdata(1)->data->user_nicename], function () {
            return response('Hello World');
        });

        $response = $this->callAuthorUrl($app, 1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    public function testRoute4()
    {
        $app = $this->createApplication();

        try {
            $app['wp.router']->is(['author.role' => 'administrator'], function () {
                return response('Hello World');
            });
        } catch (RouteConditionException $e) {
        }
    }

    public function testRoute5()
    {
        $app = $this->createApplication();

        $app['wp.router']->registerCondition('author.role', function ($role) {
            if (! is_author()) {
                return false;
            }

            $author = get_queried_object();

            return $role == $author->roles[0];
        });

        $app['wp.router']->is(['author.role' => 'administrator'], function () {
            return response('Hello World');
        });

        $response = $this->callAuthorUrl($app, 1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }
}
