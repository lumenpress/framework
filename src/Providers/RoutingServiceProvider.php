<?php

namespace LumenPress\Providers;

use LumenPress\Routing\Router;
use LumenPress\Routing\Laravel\Router as LaravelRouter;
use LumenPress\Routing\Lumen\GroupCountBasedDispatcher;

class RoutingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerRouter();
        $this->registerClassAliases();
    }

    protected function registerRouter()
    {
        $this->app->singleton('wp.router', function ($app) {
            return new Router($app);
        });

        if ($this->isLumen()) {
            $this->listen('init', function () {
                $this->app->setDispatcher($this->createDispatcher());
            });
        } else {
            $this->app->singleton('router', function ($app) {
                return new LaravelRouter($app['events'], $app);
            });
        }
    }

    protected function registerClassAliases()
    {
        $this->app->alias('wp.router', Router::class);
    }

    protected function createDispatcher()
    {
        return \FastRoute\simpleDispatcher(function ($r) {
            $routes = property_exists($this->app, 'router')
                ? $this->app->router->getRoutes()
                : $this->app->getRoutes();

            foreach ($routes as $route) {
                $r->addRoute($route['method'], $route['uri'], $route['action']);
            }
        }, ['dispatcher' => GroupCountBasedDispatcher::class]);
    }
}
