<?php

/*
|-----------------------------------------------------
| Assets
|-----------------------------------------------------
|
*/
return [

    'base_path' => get_template_directory(),

    'base_url' => get_template_directory_uri(),

    // 'editor_style' => 'editor-style.css',

    'deregister' => [],

    'dequeue' => [],

    'register' => [],

    'enqueue' => [

        'css:theme' => [
            'src' => 'assets/style.css',
        ],

        'js:theme' => [
            'src' => 'assets/main.js',
            'deps' => ['jquery'],
            'in_footer' => true,
        ],

    ],

];
