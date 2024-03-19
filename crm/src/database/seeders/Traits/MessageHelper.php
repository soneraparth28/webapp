<?php
namespace Database\Seeders\Traits;

trait MessageHelper
{
    protected $start_time;

    public function startMessage()
    {
        if (app()->runningInConsole()) {
            $this->start_time = microtime(true);
            $name = class_basename($this);
            echo  "\e[1;33mSeeding: \t $name\n";
        }
    }

    public function endMessage()
    {
        if (app()->runningInConsole()) {
            $executed = round((microtime(true) - $this->start_time), 2);
            $name = class_basename($this);
            echo "\e[1;32mSeeded: \t $name ($executed seconds)\n";
        }
    }
}
