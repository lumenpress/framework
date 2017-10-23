<?php

namespace LumenPress\Providers;

use LumenPress\Commands\InitCommand;
use LumenPress\Nimble\ServiceProvider as NimbleServiceProvider;

class LumenPressServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerCommands();

        if ($this->isLumen()) {
            $this->app->configure('nimble');
            $this->app->configure('assets');
        }

        $this->app->register(RoutingServiceProvider::class);
        $this->app->register(NimbleServiceProvider::class);
        $this->app->register(AssetServiceProvider::class);
    }

    /**
     * Register console command bindings.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->app->bindIf('command.wp.init', function () {
            return new InitCommand;
        });

        $this->commands(
            'command.wp.init'
        );
    }
}
