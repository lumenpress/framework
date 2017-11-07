<?php

return [

    /*
    |--------------------------------------------------------------------------
    | add theme support
    |--------------------------------------------------------------------------
    |
    | Features list:
    |
    | - 'post-formats'
    | - 'post-thumbnails'
    | - 'custom-background'
    | - 'custom-header'
    | - 'automatic-feed-links'
    | - 'html5'
    | - 'title-tag'
    | - 'editor-style' (internal registrations not used directly by themes)
    | - 'widgets' (internal registrations not used directly by themes)
    | - 'menus'
    |
    | @type     : [$feature => $args)
    | @default  : none
    | @function : add_theme_support
    | @see      : https://codex.wordpress.org/Function_Reference/add_theme_support
    |
    */

    'supports' => [
        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        'title-tag',
        // Add default posts and comments RSS feed links to head.
        'automatic-feed-links',
        // Enable support for Post Thumbnails on posts and pages.
        'post-thumbnails',
        'woocommerce',
        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        'html5' => [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption'
        ],
        'admin-bar' => ['callback' => '__return_false'],
        /*
         * Enable support for Post Formats.
         *
         * See: https://codex.wordpress.org/Post_Formats
         */
        /*
        'post-formats' => [
            'aside',
            'image',
            'video',
            'quote',
            'link',
            'gallery',
            'status',
            'audio',
            'chat'
        ],
        */
    ]
];
