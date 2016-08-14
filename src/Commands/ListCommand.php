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
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    protected $description = 'Lists commassnds';

    /**
     * {@inheritdoc}
     */
    public function getNativeDefinition()
    {
        return $this->createDefinition();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('list')
            ->setDefinition($this->createDefinition())
            ->setDescription('Lists commands')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command lasdfists all commands:

  <info>php %command.full_name%</info>

You can also display the commands for a specific namespace:

  <info>php %command.full_name% test</info>

You can also output the information in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml</info>

It's also possible to get raw list of commands (useful for embedding command runner):

  <info>php %command.full_name% --raw</info>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    private function createDefinition()
    {
        return new InputDefinition([
            new InputArgument('namespace', InputArgument::OPTIONAL, 'The namespace name'),
            new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command list'),
            new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getDescriptorHelper();
        $helper->describe($output, $this->getApplication(), [
            'format'    => $input->getOption('format'),
            'raw_text'  => $input->getOption('raw'),
            'namespace' => $input->getArgument('namespace'),
        ]);
    }

    /**
     * getDescriptorHelper method
     *
     * @param null|string $type (optional) The type of descriptor to use. If null, the configured value will be used
     *
     * @return \Laradic\Console\Console\Descriptor\DescriptorHelper|\Symfony\Component\Console\Helper\DescriptorHelper
     */
    protected function getDescriptorHelper($type = null)
    {
        return app()->make(DescriptorHelper::class);
    }
}
