<?php
/**
 * Copyright (c) 2016 Robin Radic.
 *
 * License can be found inside the package and is available at radic.mit-license.org.
 *
 * @author             Robin Radic
 * @copyright         Copyright (c) 2015, Robin Radic. All rights reserved
 * @license          https://radic.mit-license.org The MIT License (MIT)
 */

namespace Laradic\Console;


use Laradic\Console\Command;
use Laradic\Filesystem\Filesystem;
use Laradic\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This is the class ProvidesSubCommands.
 *
 * @package        SW
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 * @property array $commands
 * @property array $handlers
 * @property array $globalCommands
 * @mixin Command
 */
trait ProvidesSubCommands
{
    protected function resolveSubCommands(){
        $fs = Filesystem::create();
        $r = new \ReflectionClass($this);
        #$r->gx
    }

    public function fire()
    {
        $title = property_exists($this, 'title') ? $this->{'title'} : $this->getName();
        $this->writeln(" <comment>$title</comment>");
        $subtitle = str_repeat('=', strlen($title));
        $this->writeln(" <comment>$subtitle</comment>");
        $this->writeln(" <info>{$this->getDescription()}</info>");
        $this->writeln('');

        $commands = [ ];
        if ( $this->hasSubCommands() )
        {
            $this->makeSubCommandList('commands', 'class', $commands);
        }

        if ( $this->hasSubHandlers() )
        {
            $this->makeSubCommandList('handlers', 'handle', $commands);
        }

        $this->table([ ], $commands, 'compact');
        $this->writeln('');

        if ( $this->hasSubGlobals() )
        {
            $commands = [ ];
            $this->writeln(" <comment>Global</comment>");
            $this->makeSubCommandList('globalCommands', 'global', $commands);
            $this->writeln('');
            $this->table([ ], $commands, 'compact');
        }
    }

    protected function hasSubCommands()
    {
        return isset($this->commands) && is_array($this->commands) && count($this->commands) > 0;
    }

    protected function hasSubHandlers()
    {
        return property_exists($this, 'handlers') && isset($this->handlers) && is_array($this->handlers) && count($this->handlers) > 0;
    }

    protected function hasSubGlobals()
    {
        return isset($this->globalCommands) && is_array($this->globalCommands) && count($this->globalCommands) > 0;
    }

    protected function makeSubCommandList($key, $type = 'class', array &$commands)
    {
        foreach ( $this->{$key} as $k => $v )
        {
            if ( $type === 'handle' )
            {
                $name        = $k;
                $description = $v;
            }
            else
            {
                /** @var Command $command */
                $command     = app($v);
                $name        = Str::removeLeft($command->getName(), $this->getName() . ':');
                $description = $command->getDescription();
            }
            $commands[] = [ "<info>$name</info>", $description ];
        }
    }


    /**
     * run method
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     * @throws \Exception
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->modes()
            ->enable('sub-command')
            ->enable($this->getName());

        // extract real command name
        $tokens = preg_split('{\s+}', $input->__toString());
        $token = null;
        $args   = [ ];
        foreach ( $tokens as $token )
        {
            if ( $token && $token[ 0 ] !== '-' )
            {
                $args[] = $token;
                if ( count($args) >= 2 )
                {
                    break;
                }
            }
        }
        // show help for this command if no command was found
        if ( count($args) < 2 )
        {
            return parent::run($input, $output);
        }

        if ( $this->hasSubHandlers() && array_key_exists($token, $this->handlers) ) {
            $method = 'handle' . ucfirst($token);
            if(method_exists($this, $method)){
                parent::run($input, $output);
                return $this->{$method}($input->getArguments());
            }

        }
        $key = $this->modes()->isEnabled('global') ? 'globalCommands' : 'commands';
        $this->getApplication()->resolveCommands($this->{$key});

        $str   = implode('(?:', $split = str_split($this->getName())) . str_repeat(')?', count($split) - 1);
        $input = new StringInput(preg_replace('{\b' . $str . '\b\s}', $this->getName() . ':', (string)$input, 1));


        $code = $this->getApplication()->run($input, $output);


        return $code;
    }

    protected function configure()
    {
        foreach ( [ 'commands', 'globalCommands' ] as $type )
        {
            if ( ! property_exists($this, $type) )
            {
                continue;
            }
            $sc = config('sub-commands.' . $type, [ ]);
            $sc = array_merge($sc, $this->{$type});
            config()->set('sub-commands.' . $type, $sc);
        }
        #$this->getApplication()->addSubcommand($this->commands);
    }

}
