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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HelpCommand extends Command
{

    private $command;

//    protected $signature = 'help {command_name*} {--format=txt} {--raw}';

    protected $description = 'Displays help for a command';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('help')
            ->setDefinition([
                new InputArgument('command_name', InputArgument::OPTIONAL, 'The command name', 'help'),
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt'),
                new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command help'),
            ])
            ->setDescription('Displays help for a command')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command displays help for a given command:

  <info>php %command.full_name% list</info>

You can also output the help in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml list</info>

To display the list of available commands, please use the <info>list</info> command.
EOF
            );
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
        if ( null === $this->command ) {
            $this->command = $this->getApplication()->find($input->getArgument('command_name'));
        }

        $helper = new DescriptorHelper();
        $helper->describe($output, $this->command, array(
            'format' => $input->getOption('format'),
            'raw_text' => $input->getOption('raw'),
        ));

        $this->command = null;
    }

}
