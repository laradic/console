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


namespace Laradic\Console\Commands;


use Laradic\Console\Command;
use Laradic\Console\Descriptor\DescriptorHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelpCommand extends Command
{

    private $command;

    protected $signature = 'help {command_name*} {--format=txt} {--raw}';

    protected $description = 'Displays help for a command';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();
    }

    /**
     * Sets the command.
     *
     * @param Command $command The command to set
     */
    public function setCommand(\Symfony\Component\Console\Command\Command $command)
    {
        $this->command = $command;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # $this->getApplication()->registerSubCommands();

        if (null === $this->command) {
            $this->command = $this->getApplication()->find(implode(':', $input->getArgument('command_name')));
        }

        $helper = new DescriptorHelper();
        $helper->describe($output, $this->command, array(
            'format' => $input->getOption('format'),
            'raw_text' => $input->getOption('raw'),
        ));

        $this->command = null;
    }

}
