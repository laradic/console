<?php

namespace Laradic\Console;
use Illuminate\Console\Application;
use Illuminate\Console\Command as LaravelCommand;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Laradic\Console\Helpers;
use Laradic\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper as SymfonyHelpers;
use Symfony\Component\Console\Helper\HelperSet;

class Artisan extends Application
{
    /**
     * @see http://patorjk.com/software/taag/#p=display&f=Doom&t=Laradic
     * @var string
     */
    protected static $logo = <<<LOGO
 _                         _ _      
| |                       | (_)     
| |     __ _ _ __ __ _  __| |_  ___ 
| |    / _` | '__/ _` |/ _` | |/ __|
| |___| (_| | | | (_| | (_| | | (__ 
\_____/\__,_|_|  \__,_|\__,_|_|\___|
LOGO;

    protected $helpers = [
        SymfonyHelpers\FormatterHelper::class,
        SymfonyHelpers\DebugFormatterHelper::class,
        SymfonyHelpers\ProcessHelper::class,
        SymfonyHelpers\QuestionHelper::class,

        Helpers\TreeHelper::class,
        Helpers\ModesHelper::class,
        Helpers\ColorHelper::class,
    ];

    protected $fs;

    protected $modes;

    public function __construct(Container $laravel, Dispatcher $events, $version)
    {
        $this->laravel = $laravel;
        $this->fs      = new Filesystem();
        $laravel->instance('artisan', $this);

        parent::__construct($laravel, $events, $version);
        #$this->setName(app()->getName());
    }

    protected function getDefaultHelperSet()
    {
        $set             = new HelperSet;
        foreach ( $this->helpers as $helper ) {
            if ( $helper instanceof Helpers\HelperInterface && $helper::supported() === false ) {
                continue;
            }
            $set->set($this->getLaravel()->make($helper));
        }
        return $set;
    }

    protected function getDefaultCommands()
    {
        return parent::getDefaultCommands();
    }


    public function getHelp()
    {
        return self::$logo . "\n\n" . parent::getHelp();
    }

    public function add(SymfonyCommand $command)
    {
        if ( $command instanceof LaravelCommand ) {
            $command->setLaravel($this->laravel);
        }

        if ( $this->isDisabledCommand($command->getName(), 'disable') ) {
            return $command;
        }
        if ( config('app.debug', false) !== true && $this->isDisabledCommand($command->getName(), 'debug') ) {
            return $command;
        }

        return $this->addToParent($command);
    }

    public function isDisabledCommand($name, $key = 'disable')
    {
        $segments = explode(':', $name);
        return
            in_array($name, config("laradic.console.{$key}.commands", [ ]), true) ||
            in_array($segments[ 0 ], config("laradic.console.{$key}.namespaces", [ ]), true);
    }

    /**
     * getHelper method
     *
     * @param string $name
     *
     * @return \Symfony\Component\Console\Helper\HelperInterface
     */
    public function getHelper($name)
    {
        return $this->getHelperSet()->get($name);
    }

    /**
     * modes method
     * @return Helpers\ModesHelper
     */
    public function modes()
    {
        return $this->getHelper('modes');
    }

    /** @return Helpers\TreeHelper */
    public function getTreeHelper()
    {
        return $this->getHelper('tree');
    }

    /** @return Helpers\ColorHelper */
    public function getColorHelper()
    {
        return $this->getHelper('color');
    }
}
