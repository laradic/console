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

use Illuminate\Console\Command as BaseCommand;
use Laradic\Console\Helpers\ColorHelper;
use Laradic\Console\Helpers\ModesHelper;
use Laradic\Console\Helpers\TreeHelper;
use Laradic\Support\Vendor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The abstract Command class. Other commands can extend this class to benefit from a larger toolset
 *
 * @package     Laradic\Console
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 *
 */
abstract class Command extends BaseCommand
{
    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var bool
     */
    protected $allowSudo = false;

    /**
     * @var bool
     */
    protected $requireSudo = false;

    /**
     * @var \Laradic\Console\Color
     */
    protected $colors;

    /**
     * If enabled, the command will run on production environment
     * @var bool
     */
    protected $env = [];




    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the Laravel application instance.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getLaravel()
    {
        return $this->laravel;
    }

    /**
     * @param $styles
     * @param $text
     *
     * @return string
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     * @internal param array|string $style
     */
    public function colorize($styles, $text)
    {
        return $this->style($styles, $text);
    }

    /**
     * style
     *
     * @param $styles
     * @param $str
     *
     * @return string
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    protected function style($styles, $str)
    {
        return $this->color()->apply($styles, $str);
    }

    /** @return TreeHelper */
    protected function tree()
    {
        return $this->getHelper('tree');
    }

    /** @return ColorHelper */
    protected function color()
    {
        return $this->getHelper('color');
    }

    /** @return ModesHelper */
    public function modes()
    {
        return $this->getHelper('modes');
    }


    /**
     * hasRootAccess
     *
     * @return bool
     */
    public function hasRootAccess()
    {
        $path = '/root/.' . md5('_radic-cli-perm-test' . time());
        $root = (@file_put_contents($path, '1') === false ? false : true);
        if ($root !== false) {
            $this->getLaravel()->make('files')->delete($path);
        }

        return $root !== false;
    }

    /**
     * execute
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->allowSudo and !$this->requireSudo and $this->hasRootAccess()) {
            $this->error('Cannot execute this command with root privileges');
            exit;
        }

        if ($this->requireSudo and !$this->hasRootAccess()) {
            $this->error('This command requires root privileges');
            exit;
        }

        $this->fireEvent('firing', [$this]);
        $return = null;
        if (method_exists($this, 'handle')) {
            $return = $this->handle();
        }
        if (method_exists($this, 'fire')) {
            $return = $this->fire();
        }
        $this->fireEvent('fired', [$this]);

        return $return;
    }

    /**
     * @param mixed
     */
    public function dump($dump)
    {
        if (class_exists('Kint')) {
            \Kint::dump(func_get_args());
        } elseif (class_exists('Symfony\\Component\\VarDumper\\VarDumper')) {
            \Symfony\Component\VarDumper\VarDumper::dump(func_get_args());
        } else {
            var_dump(func_get_args());
        }
    }

    /**
     * arrayTable
     *
     * @param       $arr
     * @param array $header
     */
    protected function arrayTable($arr, array $header = [ 'Key', 'Value' ])
    {
        $rows = [ ];
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $val = print_r(array_slice($val, 0, 5), true);
            }
            $rows[] = [ (string)$key, (string)$val ];
        }
        $this->table($header, $rows);
    }

    /**
     * Write a string as error output.
     *
     * @param  string $string
     *
     * @return string
     */
    public function error($string, $verbosity = null)
    {
        $this->output->writeln("<error>$string</error>");

        return $string;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function choice($question, array $choices, $default = null, $attempts = null, $multiple = null)
    {
        return parent::choice($question, $choices, $default, $attempts, $multiple);
    }

    /**
     * title method
     *
     * @param $text
     *
     * @return mixed
     */
    public function title($text)
    {
        $this->output->title($text);

        return $text;
    }

    /**
     * listing method
     *
     * @param array $items
     *
     * @return array
     */
    public function listing(array $items = [ ])
    {
        $this->output->listing($items);

        return $items;
    }

    /**
     * section method
     *
     * @param $text
     *
     * @return mixed
     */
    public function section($text)
    {
        $this->output->section($text);

        return $text;
    }

    /**
     * note method
     *
     * @param $text
     *
     * @return mixed
     */
    public function note($text)
    {
        $this->output->note($text);

        return $text;
    }

    /**
     * writeln method
     *
     * @param $text
     *
     * @return mixed
     */
    public function writeln($text)
    {
        $this->output->writeln($text);

        return $text;
    }

    /**
     * write method
     *
     * @param $text
     *
     * @return mixed
     */
    public function write($text)
    {
        $this->output->write($text);

        return $text;
    }

    /**
     * success method
     *
     * @param $text
     *
     * @return mixed
     */
    public function success($text)
    {
        $this->output->success($text);

        return $text;
    }

    /**
     * warning method
     *
     * @param $text
     *
     * @return mixed
     */
    public function warning($text)
    {
        $this->output->warning($text);

        return $text;
    }

    /**
     * caution method
     *
     * @param $text
     *
     * @return mixed
     */
    public function caution($text)
    {
        $this->output->caution($text);

        return $text;
    }

    public function isEnabled()
    {
        return $this->enabled; // TODO: Change the autogenerated stub
    }

    /**
     * Set the enabled value
     *
     * @param boolean $enabled
     *
     * @return Command
     */
    public function enable()
    {
        $this->enabled = true;

        return $this;
    }

    public function disable()
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * getApplication method
     *
     * @return Artisan
     */
    public function getApplication()
    {
        return parent::getApplication(); // TODO: Change the autogenerated stub
    }

    public function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    public function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    protected function fireEvent($name, array $payload = [ ])
    {
        $events  = $this->getLaravel()->make('events');
        $command = $this->getName();
        $events->fire("command.{$name}: {$command}", array_replace_recursive([ $this ], $payload));
    }

    public function askArgument($key, $question = null, $default = null)
    {
        $arg = $this->argument($key);
        if ( $arg === null )
        {
            $question = $question ?: "{$key}?";
            $arg      = $this->ask($question, $default);
        }
        return $arg;
    }

    public function askSecretArgument($key, $question = null, $default = null)
    {
        $arg = $this->argument($key);
        if ( $arg === null )
        {
            $question = $question ?: "{$key}?";
            $arg      = $this->secret($question, $default);
        }
        return $arg;
    }

    public function askChoiceArgument($key, array $choices, $question = null, $default = null, $multi = false)
    {
        $arg = $this->argument($key);
        if ( null === $arg )
        {
            $question = $question ?: "$key?";
            $arg = parent::choice($question, $choices, $default, null, $multi);
        }
        if(false === in_array($arg, $choices, true)){
            throw new Exception("Given $key is not in " . implode(', ', $choices));
        }
        return $arg;
    }
}
