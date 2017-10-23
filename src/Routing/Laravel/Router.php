<?php

namespace LumenPress\Routing\Laravel;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router as IlluminateRouter;
use LumenPress\Routing\Router as LumenPressRouter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Router extends IlluminateRouter
{
    /**
     * Create a new Router instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(Dispatcher $events, Container $container = null)
    {
        parent::__construct($events, $container);
    }

    /**
     * Find the route matching a given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Routing\Route
     */
    protected function findRoute($request)
    {
        try {
            $this->current = $route = $this->routes->match($request);
        } catch (\Exception $e) {
            if ($e instanceof NotFoundHttpException) {
                $this->current = $route = $this->wordPressRoutesMatch($request);
            }
        }

        $this->container->instance(Route::class, $route);

        return $route;
    }

    protected function wordPressRoutesMatch($request)
    {
        if (! $this->container->bound('wp.router')) {
            throw new NotFoundHttpException;
        }

        $routeInfo = $this->container['wp.router']->dispatch($request->getMethod(), $request->path());

        if ($routeInfo[0] === LumenPressRouter::NOT_FOUND) {
            throw new NotFoundHttpException;
        }

        $route = $this->newRoute(
                $request->getMethod(), $request->path(), $routeInfo[1]
            )->bind($request);

        if (! empty($routeInfo[2])) {
            $route->setParameter(key($routeInfo[2]), current($routeInfo[2]));
        }

        return $route;
    }
}
