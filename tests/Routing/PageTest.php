<?php

namespace LumenPress\Tests\Routing;

use LumenPress\Nimble\Models\Page;

class PageTest extends TestCase
{
    /**
     * @group page
     */
    public function testRoute()
    {
        $text = 'test page route';

        $app = $this->createApplication();

        $app['wp.router']->is('page', function () use ($text) {
            return response($text);
        });

        $response = $this->callPostUrl($app, wp_insert_post([
            'post_title'    => 'this is a page',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]));

        $this->assertResponse($response, $text);

        $response = $this->callPostUrl($app, wp_insert_post([
            'post_title'    => 'this is a post',
            'post_status'   => 'publish',
            'post_type'     => 'post',
        ]));

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @group page
     */
    public function testRoute2()
    {
        $app = $this->createApplication();

        $tester = $this;

        $app['wp.router']->is('page', function ($post) use ($tester) {
            $tester->assertInstanceOf(Page::class, $post);

            return $post->id;
        });

        $id = wp_insert_post([
            'post_title'    => 'this is a page',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        $response = $this->callPostUrl($app, $id);
        $this->assertResponse($response, $id);
    }

    /**
     * @group page
     */
    public function testRoute3()
    {
        $id = wp_insert_post([
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        $app = $this->createApplication();

        $app['wp.router']->is(['page' => $id], function () use ($id) {
            return $id;
        });

        $response = $this->callPostUrl($app, $id);
        $this->assertResponse($response, $id);
    }

    /**
     * @group page
     */
    public function testRoute4()
    {
        $id = wp_insert_post([
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        $app = $this->createApplication();

        $app['wp.router']->is(['page' => get_page_uri($id)], function () use ($id) {
            return $id;
        });

        $response = $this->callPostUrl($app, $id);
        $this->assertResponse($response, $id);
    }

    /**
     * @group page
     */
    public function testRoute5()
    {
        $id2 = wp_insert_post([
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_parent'   => wp_insert_post([
                'post_status'   => 'publish',
                'post_type'     => 'page',
            ]),
        ]);

        $id1 = wp_insert_post([
            'post_title'    => get_post($id2)->post_name,
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        $app = $this->createApplication();

        $app['wp.router']->is(['page' => get_page_uri($id1)], function () use ($id1) {
            return $id1;
        });

        $app['wp.router']->is(['page' => get_page_uri($id2)], function () use ($id2) {
            return $id2;
        });

        $response = $this->callPostUrl($app, $id1);
        $this->assertResponse($response, $id1);

        $response = $this->callPostUrl($app, $id2);
        $this->assertResponse($response, $id2);
    }

    /**
     * @group page
     */
    public function testRoute6()
    {
        $id2 = wp_insert_post([
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_parent'   => wp_insert_post([
                'post_status'   => 'publish',
                'post_type'     => 'page',
            ]),
        ]);

        $id1 = wp_insert_post([
            'post_title'    => get_post($id2)->post_name,
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ]);

        $app = $this->createApplication();

        $app['wp.router']->is(['page' => [get_page_uri($id1), get_page_uri($id2)]], function () use ($id1) {
            return $id1;
        });

        $response = $this->callPostUrl($app, $id1);
        $this->assertResponse($response, $id1);

        $response = $this->callPostUrl($app, $id2);
        $this->assertResponse($response, $id1);
    }

    /**
     * @group page
     */
    public function testRoute7()
    {
        $ids = [];
        $titles[] = 'Page '.uniqid();
        $titles[] = 'Page '.uniqid();
        $titles[] = 'Page '.uniqid();

        foreach ($titles as $title) {
            $ids[] = wp_insert_post([
                'post_title'    => $title,
                'post_status'   => 'publish',
                'post_type'     => 'page',
            ]);
        }

        $app = $this->createApplication();

        $app['wp.router']->is(['page' => $titles], function () use ($ids) {
            return implode(',', $ids);
        });

        foreach ($ids as $id) {
            $response = $this->callPostUrl($app, $id);
            $this->assertResponse($response, implode(',', $ids));
        }
    }
}
