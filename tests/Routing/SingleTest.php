<?php

namespace LumenPress\Tests\Routing;

use LumenPress\Nimble\Models\Post;

class SingleTest extends TestCase
{
    /**
     * @group single
     */
    public function testRoute()
    {
        $id = wp_insert_post([
            'post_title'    => 'Post '.uniqid(),
            'post_status'   => 'publish',
            'post_type'     => 'post',
        ]);

        $app = $this->createApplication();

        $app['wp.router']->is('single', function () use ($id) {
            return response($id);
        });

        $response = $this->callPostUrl($app, $id);

        $this->assertResponse($response, $id);
    }

    /**
     * @group single
     */
    public function testRoute2()
    {
        $id = wp_insert_post([
            'post_title'    => 'Post '.uniqid(),
            'post_status'   => 'publish',
            'post_type'     => 'post',
        ]);

        $app = $this->createApplication();

        $app['wp.router']->is(['single' => 'post'], function () use ($id) {
            return response($id);
        });

        $response = $this->callPostUrl($app, $id);

        $this->assertResponse($response, $id);
    }

    /**
     * @group single
     */
    public function testRoute3()
    {
        $id = wp_insert_post([
            'post_title'    => 'Post '.uniqid(),
            'post_status'   => 'publish',
            'post_type'     => 'post',
        ]);

        $app = $this->createApplication();

        $app['wp.router']->is(['single' => $id], function () use ($id) {
            return response($id);
        });

        $response = $this->callPostUrl($app, $id);

        $this->assertResponse($response, $id);
    }

    /**
     * @group single
     */
    public function testRoute4()
    {
        $ids = [];
        for ($i = 0; $i < 3; $i++) {
            $ids[] = wp_insert_post([
                'post_title'    => 'Post '.uniqid(),
                'post_status'   => 'publish',
                'post_type'     => 'post',
            ]);
        }

        $posts = array_map(function ($id) {
            return ['post', get_post($id)->post_name];
        }, $ids);

        $app = $this->createApplication();

        $app['wp.router']->is(['single' => $posts], function () use ($ids) {
            return implode(', ', $ids);
        });

        foreach ($ids as $id) {
            $response = $this->callPostUrl($app, $id);
            $this->assertResponse($response, implode(', ', $ids));
        }
    }
}
