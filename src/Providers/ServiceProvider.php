<?php

namespace LumenPress\Providers;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected function hasWordPress()
    {
        return function_exists('add_filter');
    }

    protected function isLumen()
    {
        return stripos($this->app->version(), 'Lumen') !== false;
    }

    protected function listen($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return $this->hasWordPress() ? add_filter($tag, $function_to_add, $priority, $accepted_args) : '';
    }
}
