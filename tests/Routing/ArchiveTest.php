<?php

namespace LumenPress\Tests\Routing;

class ArchiveTest extends TestCase
{
    protected function registerPostType()
    {
        $this->setPermalinkStructure('/%year%/%monthnum%/%day%/%postname%/');

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
        ]);

        register_post_type('newspaper', [
            'label'              => 'Newspaper',
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'newspaper'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
        ]);

        flush_rewrite_rules();
    }

    public function testRoute()
    {
        $this->registerPostType();

        $app = $this->createApplication();

        $app['wp.router']->is('archive', function () {
            return response('Hello World');
        });

        $response = $this->call($app, '/book/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    public function testRoute2()
    {
        $this->registerPostType();

        $app = $this->createApplication();

        $app['wp.router']->is('post_type_archive', function () {
            return response('Hello World');
        });

        $response = $this->call($app, '/book/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    public function testRoute3()
    {
        $this->registerPostType();

        $app = $this->createApplication();

        $app['wp.router']->is(['archive' => 'book'], function ($postType) {
            return $postType->name;
        });

        $response = $this->call($app, '/book/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('book', $response->getContent());
    }

    public function testRoute4()
    {
        $this->registerPostType();

        $app = $this->createApplication();

        $postTypes = ['book', 'newspaper'];

        $app['wp.router']->is(['archive' => $postTypes], function ($postType) {
            return $postType->name;
        });

        foreach ($postTypes as $postType) {
            $response = $this->call($app, "/{$postType}/");

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals($postType, $response->getContent());
        }
    }
}
