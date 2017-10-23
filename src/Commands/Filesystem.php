<?php

namespace LumenPress\Commands;

class Filesystem
{
    protected $baseDir;

    protected $vendorDir;

    public function __construct($baseDir = null)
    {
        $this->baseDir = $baseDir ?: base_path().'/';
        $this->vendorDir = $this->baseDir.'vendor/';
    }

    public function exists($file)
    {
        return file_exists(base_path($file));
    }

    public function isDir($dir)
    {
        return is_dir(base_path($dir));
    }

    public function packageExists($package)
    {
        return is_dir(base_path("vendor/$package"));
    }

    public function packagePath($src)
    {
        return base_path("vendor/$src");
    }

    public function path($src)
    {
        return base_path($src);
    }

    public function file($file)
    {
        return file(base_path($file));
    }

    public function unlink($file)
    {
        unlink(base_path($file));
    }

    public function get($file)
    {
        return file_get_contents(base_path($file));
    }

    public function put($file, $data)
    {
        if (! is_dir(dirname(base_path($file)))) {
            @mkdir(dirname(base_path($file)), 0777, true);
        }
        file_put_contents(base_path($file), $data);
    }

    public function replace($file, array $replaces)
    {
        $data = $this->get($file);

        foreach ($replaces as $search => $replace) {
            if (@preg_match($search, null) !== false) {
                $data = preg_replace($search, $replace, $data);
            } else {
                $data = str_replace($search, $replace, $data);
            }
        }

        $this->put($file, $data);
    }

    public function stub($file, $context = [])
    {
        $content = file_get_contents(__DIR__.'/stubs/'.$file);

        foreach ($context as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        return $content;
    }

    public function match($pattern, $file)
    {
        if (@preg_match($pattern, null) === false) {
            $pattern = '/'.str_replace('/', '\/', preg_quote($pattern)).'/';
        }

        return preg_match($pattern, $this->get($file), $matches);
    }

    public function copy($source, $dest)
    {
        copy(base_path($source), base_path($dest));
        echo "Copied file [$source] to [$dest]\n";
    }
}
