<?php

namespace LumenPress\Tests\Routing;

class TagTest extends TestCase
{
    /**
     * @group tag
     */
    public function testRoute()
    {
        $tag = wp_insert_term('tag '.uniqid(), 'post_tag');

        $app = $this->createApplication();

        $app['wp.router']->is('tag', function () {
            return response('Hello World');
        });

        $response = $this->callTaxonomyUrl($app, $tag['term_id'], 'post_tag');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group tag
     */
    public function testRoute2()
    {
        $tag = wp_insert_term('tag '.uniqid(), 'post_tag');

        $app = $this->createApplication();

        $app['wp.router']->is(['tag' => $tag['term_id']], function () {
            return response('Hello World');
        });

        $response = $this->callTaxonomyUrl($app, $tag['term_id'], 'post_tag');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group tag
     */
    public function testRoute3()
    {
        $tag = wp_insert_term('tag '.uniqid(), 'post_tag');

        $term = get_term($tag['term_id']);

        $app = $this->createApplication();

        $app['wp.router']->is(['tag' => $term->slug], function () {
            return response('Hello World');
        });

        $response = $this->callTaxonomyUrl($app, $tag['term_id'], 'post_tag');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group tag
     */
    public function testRoute4()
    {
        $tags[] = wp_insert_term('tag '.uniqid(), 'post_tag');
        $tags[] = wp_insert_term('tag '.uniqid(), 'post_tag');
        $tags[] = wp_insert_term('tag '.uniqid(), 'post_tag');

        $ids = array_map(function ($tag) {
            return $tag['term_id'];
        }, $tags);

        $app = $this->createApplication();

        $app['wp.router']->is(['tag' => $ids], function () {
            return response('Hello World');
        });

        foreach ($ids as $id) {
            $response = $this->callTaxonomyUrl($app, $id, 'post_tag');
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals('Hello World', $response->getContent());
        }
    }
}
