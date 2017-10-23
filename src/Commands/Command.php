<?php

namespace LumenPress\Commands;

class Command extends \Illuminate\Console\Command
{
    protected $fs;

    public function __construct()
    {
        parent::__construct();
        $this->fs = new Filesystem;
        $this->composer = new Composer;
    }

    public function fire()
    {
        $this->handle();
    }
}
