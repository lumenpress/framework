<?php

namespace LumenPress\Tests\Routing;

class TemplateTest extends TestCase
{
    public function testRoute()
    {
        $text = 'test template route';

        $app = $this->createApplication();

        $app['wp.router']->is(['template' => 'default'], function () use ($text) {
            return response($text);
        });

        $posts = [
            [
                'post_title'    => 'this is a post',
                'post_status'   => 'publish',
                'post_type'     => 'post',
            ],
            [
                'post_title'    => 'this is a page',
                'post_status'   => 'publish',
                'post_type'     => 'page',
            ],
        ];

        foreach ($posts as $post) {
            $response = $this->callPostUrl($app, wp_insert_post($post));
            $this->assertResponse($response, $text);
        }
    }

    public function testRoute2()
    {
        $app = $this->createApplication();

        $app['wp.router']->is(['template' => 'home'], function () {
            return response('test template route 2');
        });

        foreach (['page', 'post'] as $key => $postType) {
            $id = wp_insert_post([
                'post_title'    => 'test template route',
                'post_status'   => 'publish',
                'post_type'     => $postType,
                'meta_input'    => [
                    '_wp_page_template' => 'home',
                ],
            ]);

            $response = $this->callPostUrl($app, $id);
            $this->assertResponse($response, 'test template route 2');
        }
    }

    public function testRoute4()
    {
        $app = $this->createApplication();

        $app['wp.router']->is(['template' => ['about', 'contact']], function ($post) {
            return 'test singular route 4.0';
        });

        $posts = [
            [
                'post_title'    => 'test singular route 4.1',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'meta_input'    => [
                    '_wp_page_template' => 'about',
                ],
            ],
            [
                'post_title'    => 'test singular route 4.2',
                'post_status'   => 'publish',
                'post_type'     => 'post',
                'meta_input'    => [
                    '_wp_page_template' => 'about',
                ],
            ],
            [
                'post_title'    => 'test singular route 4.3',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'meta_input'    => [
                    '_wp_page_template' => 'contact',
                ],
            ],
            [
                'post_title'    => 'test singular route 4.4',
                'post_status'   => 'publish',
                'post_type'     => 'post',
                'meta_input'    => [
                    '_wp_page_template' => 'contact',
                ],
            ],
        ];

        foreach ($posts as $post) {
            $response = $this->callPostUrl($app, wp_insert_post($post));
            $this->assertResponse($response, 'test singular route 4.0');
        }
    }
}
