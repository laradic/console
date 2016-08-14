<?php
namespace Laradic\Console;

use Laradic\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    protected $scanDirs = true;

    protected $configFiles = [ 'laradic.console' ];

    public function register()
    {


        $a = 'a';
    }
}
