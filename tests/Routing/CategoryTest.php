<?php

namespace LumenPress\Tests\Routing;

class CategoryTest extends TestCase
{
    /**
     * @group category
     */
    public function testRoute()
    {
        $category = wp_insert_term('category '.uniqid(), 'category');

        $app = $this->createApplication();

        $app['wp.router']->is('category', function () {
            return response('Hello World');
        });

        $response = $this->callTaxonomyUrl($app, $category['term_id'], 'category');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group category
     */
    public function testRoute2()
    {
        $category = wp_insert_term('category '.uniqid(), 'category');

        $app = $this->createApplication();

        $app['wp.router']->is(['category' => $category['term_id']], function () {
            return response('Hello World');
        });

        $response = $this->callTaxonomyUrl($app, $category['term_id'], 'category');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group category
     */
    public function testRoute3()
    {
        $category = wp_insert_term('category '.uniqid(), 'category');

        $term = get_term($category['term_id']);

        $app = $this->createApplication();

        $app['wp.router']->is(['category' => $term->slug], function () {
            return response('Hello World');
        });

        $response = $this->callTaxonomyUrl($app, $category['term_id'], 'category');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group category
     */
    public function testRoute4()
    {
        $categories[] = wp_insert_term('category '.uniqid(), 'category');
        $categories[] = wp_insert_term('category '.uniqid(), 'category');
        $categories[] = wp_insert_term('category '.uniqid(), 'category');

        $ids = array_map(function ($category) {
            return $category['term_id'];
        }, $categories);

        $app = $this->createApplication();

        $app['wp.router']->is(['category' => $ids], function () {
            return response('Hello World');
        });

        foreach ($ids as $id) {
            $response = $this->callTaxonomyUrl($app, $id, 'category');
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals('Hello World', $response->getContent());
        }
    }
}
