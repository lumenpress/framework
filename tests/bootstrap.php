<?php

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

$path = realpath(dirname(PHPUNIT_COMPOSER_INSTALL).'/lumenpress/testing');

system("php $path/tests/includes/install.php");

require $path.'/tests/wp-tests-load.php';
