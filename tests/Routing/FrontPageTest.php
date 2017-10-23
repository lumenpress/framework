<?php

namespace LumenPress\Tests\Routing;

use LumenPress\Nimble\Models\Page;

class FrontPageTest extends TestCase
{
    public function testRoute()
    {
        $this->setPermalinkStructure('/%year%/%monthnum%/%day%/%postname%/');

        $app = $this->createApplication();

        $app['wp.router']->is('front', function () {
            return response('Hello World');
        });

        $response = $this->call($app, '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    public function testRoute2()
    {
        $id = wp_insert_post([
            'post_title'    => 'this is a page',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        update_option('show_on_front', 'page');
        update_option('page_on_front', $id);

        $app = $this->createApplication();

        $tester = $this;

        $app['wp.router']->is('front', function ($post) use ($tester) {
            $tester->assertInstanceOf(Page::class, $post);

            return $post->id;
        });

        $response = $this->call($app, '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($id, $response->getContent());

        update_option('show_on_front', 'posts');
    }
}
