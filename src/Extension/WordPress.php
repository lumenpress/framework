<?php

namespace LumenPress\Extension;

use Twig_Extension;
use LumenPress\Helper;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use LumenPress\Nimble\Models\Menu;
use LumenPress\Nimble\Models\Option;
use Twig_Extension_GlobalsInterface;

class WordPress extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'LumenPress_Extension_WordPress';
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [
            'wp' => Helper::wrap(['wp_', 'wp_get_'], [
                // 'options' => function () {
                //     return Option::getInstance();
                // },
                // 'menus' => function () {
                //     return Menu::getInstance();
                // },
            ]),
            'is' => Helper::wrap(['is_', 'wp_is_'], [
                'php' => true,
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('site_url', 'home_url'),
            new Twig_SimpleFilter('asset_url', [$this, 'assetUrl'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('site_url', 'home_url'),
            new Twig_SimpleFunction('asset_url', [$this, 'assetUrl'], ['is_safe' => ['html']]),
        ];
    }

    public function assetUrl($src)
    {
        return config('assets.base_url').'/'.$src;
    }
}
