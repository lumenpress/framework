<?php

namespace LumenPress\Tests\Routing;

use LumenPress\Nimble\Models\Post;

class SingularTest extends TestCase
{
    /**
     * is_singular().
     *
     * @group singular
     */
    public function testRoute()
    {
        $this->setPermalinkStructure('/%year%/%monthnum%/%day%/%postname%/');

        $app = $this->createApplication();

        $app['wp.router']->is('singular', function () {
            return response('test singular route');
        });

        $post = wp_insert_post([
            'post_title'    => 'test singular route',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        $response = $this->callPostUrl($app, $post);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test singular route', $response->getContent());
    }

    /**
     * is_singular().
     *
     * @group singular
     */
    public function testRoute2()
    {
        $app = $this->createApplication();

        $tester = $this;

        $app['wp.router']->is('singular', function ($post) use ($tester) {
            $tester->assertInstanceOf(Post::class, $post);

            return $post->id;
        });

        $id = wp_insert_post([
            'post_title'    => 'test singular route 2',
            'post_status'   => 'publish',
            'post_type'     => 'post',
        ]);

        $response = $this->callPostUrl($app, $id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($id, $response->getContent());
    }

    public function testRoute3()
    {
        $app = $this->createApplication();

        $app['wp.router']->is(['singular' => 'page'], function ($post) {
            return $post->title;
        });

        $id = wp_insert_post([
            'post_title'    => 'test singular route 3',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        $response = $this->callPostUrl($app, $id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test singular route 3', $response->getContent());
    }

    public function testRoute4()
    {
        $app = $this->createApplication();

        $app['wp.router']->is(['singular' => 'post'], function ($post) {
            return $post->title;
        });

        $id = wp_insert_post([
            'post_title'    => 'test singular route 4',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        $response = $this->callPostUrl($app, $id);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testRoute5()
    {
        $app = $this->createApplication();

        $app['wp.router']->is(['singular' => 'page'], function ($post) {
            return 'test singular route 5.1';
        });

        $app['wp.router']->is('singular', function ($post) {
            return 'test singular route 5.2';
        });

        $id = wp_insert_post([
            'post_title'    => 'test singular route 5.1',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        $response = $this->callPostUrl($app, $id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test singular route 5.1', $response->getContent());

        $id = wp_insert_post([
            'post_title'    => 'test singular route 5.2',
            'post_status'   => 'publish',
        ]);

        $response = $this->callPostUrl($app, $id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test singular route 5.2', $response->getContent());
    }

    public function testRoute6()
    {
        register_post_type('book', [
            'label'              => 'Book',
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'book'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'],
        ]);

        flush_rewrite_rules();

        $id = wp_insert_post([
            'post_title'    => 'test singular route 6',
            'post_status'   => 'publish',
            'post_type'     => 'book',
        ]);

        $app = $this->createApplication();

        $app['wp.router']->is(['singular' => 'book'], function ($post) {
            return 'is book post type';
        });

        $response = $this->callPostUrl($app, $id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('is book post type', $response->getContent());
    }

    public function testRoute7()
    {
        $app = $this->createApplication();

        $app['wp.router']->is(['singular' => ['page', 'post']], function ($post) {
            return 'test singular route 7';
        });

        $id = wp_insert_post([
            'post_title'    => 'test singular route 7.1',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        $response = $this->callPostUrl($app, $id);
        $this->assertResponse($response, 'test singular route 7');

        $id = wp_insert_post([
            'post_title'    => 'test singular route 7.2',
            'post_status'   => 'publish',
            'post_type'     => 'post',
        ]);

        $response = $this->callPostUrl($app, $id);
        $this->assertResponse($response, 'test singular route 7');
    }
}
