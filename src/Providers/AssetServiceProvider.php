<?php

namespace LumenPress\Providers;

class AssetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! $this->hasWordPress()) {
            return;
        }

        $this->loadConfiguration();
        $this->simplifyAdminBarMenu();
        $this->loadAssets();
    }

    public function loadAssets()
    {
        $this->listen('wp_enqueue_scripts', function () {
            foreach (config('assets.enqueue') as $key => $args) {
                $this->enqueue($this->parseAssetArgs($key, $args));
            }
        }, 9999);
    }

    protected function enqueue($args)
    {
        if (isset($args['if'])) {
            if (! (is_callable($args['if']) && call_user_func($args['if']))) {
                return;
            }
        }

        if (isset($args['media'])) {
            wp_enqueue_style($args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media']);
        } else {
            wp_enqueue_script($args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer']);
        }
    }

    protected function parseAssetArgs($key, $args)
    {
        if (is_string($args)) {
            $args = is_numeric($key) ? ['handle' => $args] : ['handle' => $key, 'src' => $args];
        } else {
            $args['handle'] = $key;
        }

        $args['src'] = config('assets.base_url').'/'.$args['src'];

        $type = stripos($args['handle'], 'css:') === 0 ? 'css:' : 'js:';
        $args['handle'] = str_replace($type, '', $args['handle']);

        return $type === 'css:' ? array_merge([
                'src' => '',
                'deps' => [],
                'ver' => null,
                'media' => 'all',
            ], $args) : array_merge([
            'src' => '',
            'deps' => [],
            'ver' => false,
            'in_footer' => false,
        ], $args);
    }

    /**
     * Load the configuration files and allow them to be published.
     *
     * @return void
     */
    protected function loadConfiguration()
    {
        $path = __DIR__.'/../../config/assets.php';

        if (! $this->isLumen()) {
            $this->publishes([$path => config_path('assets.php')], 'config');
        }

        $this->mergeConfigFrom($path, 'assets');
    }

    public function simplifyAdminBarMenu()
    {
        $this->listen('wp_enqueue_scripts', function () {
            wp_deregister_style('admin-bar');
            wp_deregister_script('admin-bar');
            wp_enqueue_style(
                'admin-bar',
                'https://unpkg.com/admin-bar/style.css',
                ['dashicons']
            );
        }, 9998);

        $this->listen('admin_bar_menu', function ($admin_bar) {
            if (is_admin() || ! is_admin_bar_showing()) {
                return;
            }

            $admin_bar->remove_node('wp-logo');
            $admin_bar->remove_node('customize');
            $admin_bar->remove_node('updates');
            $admin_bar->remove_node('comments');
            $admin_bar->remove_node('search');
            $admin_bar->remove_node('my-account');

            return $admin_bar;
        }, 9999);

        if (function_exists('add_theme_support')) {
            add_theme_support('admin-bar', ['callback' => '__return_false']);
        }
    }
}
