<?php
/**
 * Part of the $author$ PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */


namespace Laradic\Console\IO;

use Laradic\Console\Exceptions\DependencyException;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput as BaseOutput;

class ConsoleOutput extends BaseOutput
{
    protected $decorated;
    protected $verbosity;

    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null)
    {
        if(!class_exists('Hoa\Console\Console')){
            throw DependencyException::missing('hoathis/symfony-console-bridge');
        }
        parent::__construct($verbosity, $decorated, $formatter);
    }


    protected function hasColorSupport()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
        }

        return forward_static_call('Hoa\Console\Console::isDirect', $this->getStream());
    }

    public function setDecorated($decorated)
    {
        parent::setDecorated($this->decorated = $decorated);

        return $this;
    }

    public function isDecorated()
    {
        if (null === $this->decorated) {
            $this->decorated = $this->hasColorSupport();

            #$this->setDecorated($this->decorated);
        }

        return $this->decorated;
    }

    public function setVerbosity($level)
    {
        parent::setVerbosity($this->verbosity = $level);

        return $this;
    }

    public function getVerbosity()
    {
        if (null === $this->verbosity) {
            $stream = $this->getStream();

            switch (true) {
                case forward_static_call('Hoa\Console\Console::isDirect', $stream):
                    $level = OutputInterface::VERBOSITY_VERBOSE;
                    break;

                case forward_static_call('Hoa\Console\Console::isRedirection', $stream):
                    $level = OutputInterface::VERBOSITY_VERY_VERBOSE;
                    break;

                default:
                    $level = OutputInterface::VERBOSITY_NORMAL;
            }

            $this->setVerbosity($level);
        }

        return $this->verbosity;
    }

    public function setFormatter(OutputFormatterInterface $formatter)
    {
        $formatter->setDecorated($this->isDecorated());

        parent::setFormatter($formatter);

        return $this;
    }
}
