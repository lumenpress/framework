<?php

namespace LumenPress\Providers;

class ThemeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! $this->hasWordPress()) {
            return;
        }

        $this->loadConfiguration();
        $this->registerSupports();
    }

    /**
     * Load the configuration files and allow them to be published.
     *
     * @return void
     */
    protected function loadConfiguration()
    {
        $path = __DIR__.'/../../config/theme.php';

        if (! $this->isLumen()) {
            $this->publishes([$path => config_path('theme.php')], 'config');
        }

        $this->mergeConfigFrom($path, 'theme');
    }

    public function registerSupports()
    {
        add_action('after_setup_theme', function () {
            // This theme styles the visual editor to resemble the theme style.
            if ($supports = config('theme.supports')) {
                foreach ($supports as $feature => $args) {
                    if (is_numeric($feature)) {
                        $args = array($args);
                    } else {
                        $args = array($feature, $args);
                    }
                    call_user_func_array('add_theme_support', $args);
                }
            }

            if ($image_size = config('theme.image-size')) {
                foreach ($image_size as $args) {
                    call_user_func_array('add_image_size', $args);
                }
            }
        });
    }
}
