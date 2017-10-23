<?php

namespace LumenPress\Commands;

class InitCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'wp:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize wordpress';

    protected $type;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->confirm('Do you wish to continue?')) {
            return;
        }

        $this->type = $this->choice('As a project or theme?', ['project', 'theme'], 0);

        if ($this->type === 'project') {
            if (! $this->composer->config('extra.wordpress-install-dir')) {
                $installDir = $this->ask('wordpress install dir', 'public/wp');
                $contentDir = $this->ask('wordpress content dir', 'public/content');
                $this->composer->extra([
                    'wordpress-install-dir' => $installDir,
                    'installer-paths' => [
                        $contentDir.'/plugins/{$name}' => ['type:wordpress-plugin'],
                        $contentDir.'/mu-plugins/{$name}' => ['type:wordpress-muplugin'],
                        $contentDir.'/themes/{$name}' => ['type:wordpress-theme'],
                    ],
                ]);
                $this->composer->require('johnpbloch/wordpress');
            }

            $wpdir = basename($this->composer->config('extra.wordpress-install-dir'));

            $this->fs->put('public/index.php', $this->fs->stub('wp-index.stub', ['wpdir' => $wpdir]));
            $this->fs->put('public/wp-config.php', $this->fs->stub('wp-config.stub', ['wpdir' => $wpdir]));
        } else {
            $this->fs->replace('artisan', [
                '/\$app = require(_once)? __DIR__.\'\/bootstrap\/app.php\';/' => $this->fs->stub('artisan.stub'),
                '$app' => 'app()',
            ]);
        }

        $this->installTheme();

        if (! $this->composer->config('require.lumenpress/acf')
            && $this->confirm('Installing the ACF plugin?')) {
            $this->composer->require('lumenpress/acf');
        }

        if (! $this->composer->config('require.rcrowe/twigbridge')
            && $this->confirm('Installing the Twig template engine?')) {
            $this->composer->require('rcrowe/twigbridge');
        }

        $this->composer->update();

        $this->line('Hello World!');
    }

    public function installTheme()
    {
        if ($this->fs->isDir('vendor/laravel/framework')) {
            $name = 'Laravel';
            $data = $this->fs->get('vendor/laravel/framework/src/Illuminate/Foundation/Application.php');
            $pattern = "/const\sVERSION\s=\s'([0-9.]+)';/i";
        } else {
            $name = 'Lumen';
            $data = $this->fs->get('vendor/laravel/lumen-framework/src/Application.php');
            $pattern = "/\'Lumen \(([0-9.]+)\)/i";
        }

        $themeDir = $this->type === 'theme' ? 'public' : 'public/content/themes/'.strtolower($name);

        if ($this->fs->exists("$themeDir/functions.php")
            && ! $this->confirm('Do you wish to replace the theme files?')) {
            return;
        }

        preg_match($pattern, $data, $matches);

        $info = [
            'ThemeName' => $name,
            'Description' => "A WordPress theme using the $name Framework.",
            'Version' => $matches[1],
        ];

        $input = [];

        foreach ($info as $key => $value) {
            $input[$key] = $this->ask($key, $value) ?: $value;
        }

        $this->fs->put("$themeDir/style.css", $this->fs->stub('style.stub', $input));
        $this->fs->put("$themeDir/index.php", $this->fs->stub('index.stub'));

        if ($name == 'Lumen') {
            $this->fs->put("$themeDir/functions.php", $this->fs->stub('lumen-functions.stub', [
                'environments' => $this->fs->stub('putenv.stub'),
            ]));
        } else {
            $this->fs->put("$themeDir/functions.php", $this->fs->stub('laravel-functions.stub', [
                'environments' => $this->fs->stub('putenv.stub'),
                'autoloadFile' => $this->fs->exists('bootstrap/autoload.php')
                    ? 'bootstrap/autoload.php'
                    : 'vendor/autoload.php',
            ]));
        }
    }
}
