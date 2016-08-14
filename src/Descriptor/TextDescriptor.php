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
namespace Laradic\Console\Descriptor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Descriptor\ApplicationDescription;
use Symfony\Component\Console\Input\InputDefinition;

class TextDescriptor extends \Symfony\Component\Console\Descriptor\TextDescriptor
{

    /**
     * {@inheritdoc}
     */
    private function writeText($content, array $options = array())
    {
        $this->write(
            isset($options['raw_text']) && $options['raw_text'] ? strip_tags($content) : $content,
            isset($options['raw_output']) ? !$options['raw_output'] : true
        );
    }

    /**
     * @param Command[] $commands
     *
     * @return int
     */
    private function getColumnWidth(array $commands)
    {
        $widths = array();

        foreach ($commands as $command) {
            $widths[] = strlen($command->getName());
            foreach ($command->getAliases() as $alias) {
                $widths[] = strlen($alias);
            }
        }

        return max($widths) + 2;
    }

    /**
     * {@inheritdoc}
     */
    protected function describeApplication(Application $application, array $options = array())
    {
        $describedNamespace = isset($options['namespace']) ? $options['namespace'] : null;
        $description = new ApplicationDescription($application, $describedNamespace);

        if (isset($options['raw_text']) && $options['raw_text']) {
            $width = $this->getColumnWidth($description->getCommands());

            foreach ($description->getCommands() as $command) {
                $this->writeText(sprintf("%-${width}s %s", $command->getName(), $command->getDescription()), $options);
                $this->writeText("\n");
            }
        } else {
            if ('' != $help = $application->getHelp()) {
                $this->writeText("$help\n\n", $options);
            }

            $this->writeText("<comment>Usage:</comment>\n", $options);
            $this->writeText("  command [options] [arguments]\n\n", $options);

            $this->describeInputDefinition(new InputDefinition($application->getDefinition()->getOptions()), $options);

            $this->writeText("\n");
            $this->writeText("\n");

            $width = $this->getColumnWidth($description->getCommands());

            if ($describedNamespace) {
                $this->writeText(sprintf('<comment>Available commands for the "%s" namespace:</comment>', $describedNamespace), $options);
            } else {
                $this->writeText('<comment>Available commands:</comment>', $options);
            }

            // add commands by namespace
            foreach ($description->getNamespaces() as $namespace) {
                if ( in_array($namespace[ 'id' ], config('laradic.console.hide.namespaces', [ ]), true) ) {
                    continue;
                }
                if (!$describedNamespace && ApplicationDescription::GLOBAL_NAMESPACE !== $namespace['id']) {
                    $this->writeText("\n");
                    $this->writeText(' <comment>'.$namespace['id'].'</comment>', $options);
                }

                foreach ($namespace['commands'] as $name) {
                    if ( in_array($name, config('laradic.console.hide.commands', [ ]), true) ) {
                        continue;
                    }
                    $this->writeText("\n");
                    $spacingWidth = $width - strlen($name);
                    $this->writeText(sprintf('  <info>%s</info>%s%s', $name, str_repeat(' ', $spacingWidth), $description->getCommand($name)->getDescription()), $options);
                }
            }

            $this->writeText("\n");
        }
    }

}
