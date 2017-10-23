<?php

namespace LumenPress\Tests\Routing;

class TaxonomyTest extends TestCase
{
    protected function registerTaxonomy()
    {
        $this->setPermalinkStructure('/%year%/%monthnum%/%day%/%postname%/');

        register_taxonomy('genre', 'post', [
            'label'        => 'Genre',
            'public'       => true,
            'rewrite'      => ['slug' => 'genre'],
            'hierarchical' => true,
        ]);

        flush_rewrite_rules();
    }

    /**
     * @group tax
     */
    public function testRoute()
    {
        $this->registerTaxonomy();

        $term = wp_insert_term('tax '.uniqid(), 'genre');

        $app = $this->createApplication();

        $app['wp.router']->is('tax', function () {
            return response('Hello World');
        });

        $response = $this->callTaxonomyUrl($app, $term['term_id'], 'genre');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group tax
     */
    public function testRoute2()
    {
        $this->registerTaxonomy();

        $term = wp_insert_term('tax '.uniqid(), 'genre');

        $app = $this->createApplication();

        $app['wp.router']->is(['tax' => 'genre'], function () {
            return response('Hello World');
        });

        $response = $this->callTaxonomyUrl($app, $term['term_id'], 'genre');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }

    /**
     * @group tax
     */
    public function testRoute3()
    {
        $this->registerTaxonomy();

        $term = wp_insert_term('tax '.uniqid(), 'genre');

        $t = get_term($term['term_id']);

        $app = $this->createApplication();

        $taxonomies = [
            ['genre', $t->slug],
        ];

        $app['wp.router']->is(['tax' => $taxonomies], function () {
            return response('Hello World');
        });

        $response = $this->callTaxonomyUrl($app, $term['term_id'], 'genre');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getContent());
    }
}
