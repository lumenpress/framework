<?php

namespace LumenPress\Commands;

class Composer
{
    public function __construct()
    {
        $this->fs = new Filesystem;
        $this->config = json_decode($this->fs->get('composer.json'), true);
    }

    public function hasConfig($key)
    {
        return array_has($this->config, $key);
    }

    public function vendorExists($package)
    {
        return $this->fs->exists('/vendor/'.$package);
    }

    public function config($key)
    {
        return array_get($this->config, $key);
    }

    public function extra($config)
    {
        if (! isset($this->config['extra'])) {
            $this->config['extra'] = [];
        }
        $this->config['extra'] = array_merge($this->config['extra'], $config);
    }

    public function require($package, $version = '*')
    {
        $this->config['require'][$package] = $version;
    }

    public function update()
    {
        $this->fs->put('composer.json', $this->content());
    }

    public function content()
    {
        return str_replace('\/', '/', json_encode($this->config, JSON_PRETTY_PRINT));
    }
}
