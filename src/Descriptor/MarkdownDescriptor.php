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

use InvalidArgumentException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Descriptor\ApplicationDescription;
use Symfony\Component\Console\Descriptor\MarkdownDescriptor as BaseMarkdownDescriptor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MarkdownDescriptor extends BaseMarkdownDescriptor
{

    /**
     * Writes content to output.
     *
     * @param string $content
     * @param bool   $decorated
     */
    protected function write($content, $decorated = false)
    {
        $this->output->write($content, false, $decorated ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_RAW);
    }

    /**
     * {@inheritdoc}
     */
    public function describe(OutputInterface $output, $object, array $options = [ ])
    {
        $this->output = $output;

        switch ( true ) {
            case $object instanceof InputArgument:
                $this->describeInputArgument($object, $options);
                break;
            case $object instanceof InputOption:
                $this->describeInputOption($object, $options);
                break;
            case $object instanceof InputDefinition:
                $this->describeInputDefinition($object, $options);
                break;
            case $object instanceof Command:
                $this->describeCommand($object, $options);
                break;
            case $object instanceof Application:
                $this->describeApplication($object, $options);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Object of type "%s" is not describable.', get_class($object)));
        }
    }


    /**
     * {@inheritdoc}
     */
    protected function describeInputDefinition(InputDefinition $definition, array $options = [ ])
    {
        if ( $showArguments = count($definition->getArguments()) > 0 ) {
            $rows = [ ];
            foreach ( $definition->getArguments() as $argument ) {
                $extra = '';
                $extra .= $argument->isRequired() ? '`required`&nbsp;' : '<span class="tag-optional">`optional`</span>&nbsp;';
                $extra .= $argument->isArray() ? '`array()`&nbsp;' : '';
                $str    = is_array($argument->getDefault()) ? implode(' ', $argument->getDefault()) : $argument->getDefault();
                $rows[] = [ "`[<{$argument->getName()}>]`", $this->col($argument->getDescription()), $this->col($str), $this->col($extra) ];
            }
            if ( count($rows) > 0 ) {
                $this->write('### Arguments:');
                $this->write($this->table([ 'Name', 'Description', 'Default', 'Extra' ], $rows));
            }
        }

        // | Name | Description | Default | Extra |

        $skipOptions = [ 'help', 'quiet', 'verbose', 'version', 'ansi', 'no-ansi', 'no-interaction', 'env' ];
        if ( count($definition->getOptions()) > 0 ) {
            if ( $showArguments ) {
                $this->write("\n\n");
            }

            $rows = [ ];
            foreach ( $definition->getOptions() as $option ) {
                if ( in_array($option->getName(), $skipOptions, true) ) {
                    continue;
                }
                $name = '`[--' . $option->getName();
                if ( $option->acceptValue() ) {
                    $default = $option->getDefault();
                    if ( is_array($default) ) {
                        $default = implode(',', $default);
                    }
                    $name .= '=' . $default;
                }
                $name .= ']`';

                $extra = '';
                $extra .= $option->isValueOptional() ? '<span class="tag-optional">`optional`</span> ' : '';
                $extra .= $option->isValueRequired() ? '`required` ' : '';
                $extra .= $option->isArray() ? '`array`' : '';

                $shortcut = ($option->getShortcut() ? '`-' . implode('|-', explode('|', $option->getShortcut())) . '`' : '');

                $rows[] = [ $name, $this->col($option->getDescription()), $this->col($shortcut), $this->col($extra) ];
            }
            if ( count($rows) > 0 ) {
                $this->write('### Options:');
                $this->write($this->table([ 'Name', 'Description', 'Shortcut', 'Extra' ], $rows));
            }
        }
    }

    protected function col($txt)
    {
        if ( strlen(trim($txt)) === 0 ) {
            return '-';
        }
        return $txt;
    }

    protected function table($headers = [ ], $rows = [ ])
    {
        $nl   = "\n";
        $text = $nl . '| ' . implode(' | ', $headers) . ' |';
        $text .= $nl . implode('', array_fill(0, count($headers), '|:--------')) . '|';

        foreach ( $rows as $row ) {
            $text .= $nl . '| ' . implode(' | ', $row) . ' |';
        }

        return $text;
    }

    protected $slugs = [ ];

    /**
     * {@inheritdoc}
     */
    protected function describeApplication(Application $application, array $options = [ ])
    {
        $describedNamespace = isset($options[ 'namespace' ]) ? $options[ 'namespace' ] : null;
        $description        = new ApplicationDescription($application, $describedNamespace);

        $this->write($application->getName() . "\n" . str_repeat('=', strlen($application->getName())));

        $transformCommandName = function ($commandName) {
            $slug = str_replace(' ', '-', $commandName);
            if ( isset($this->slugs[ $slug ]) ) {
                $slug .= $this->slugs[ $slug ] + 1;
            } else {
                $this->slugs[ $slug ] = 0;
            }
            return '[`' . $commandName . '`](#' . $slug . ')';
        };

        foreach ( $description->getNamespaces() as $namespace ) {
            if ( ApplicationDescription::GLOBAL_NAMESPACE !== $namespace[ 'id' ] ) {
                $this->write("\n\n");
                $this->write('**' . $namespace[ 'id' ] . ':**');
            }
            $rows = [ ];
            foreach ( $namespace[ 'commands' ] as $commandName ) {
                $command = $description->getCommand($commandName);
                $desc    = $command->getDescription();
                $rows[]  = [
                    $transformCommandName($commandName), // name
                    $desc === '' ? '-' : $desc,
                    '-',
                ];
            }

            $this->write($this->table([ 'Name', 'Description', '-' ], $rows));
            //$this->describeInputDefinition($command->getNativeDefinition());

            // |  |  |

            $this->write("\n\n");
//            $this->write(implode("\n", array_map(function ($commandName) {
//                $slug = str_replace(' ', '-', $commandName);
//                if ( isset($this->slugs[ $slug ]) ) {
//                    $slug .= $this->slugs[ $slug ] + 1;
//                } else {
//                    $this->slugs[ $slug ] = 0;
//                }
//                return '* [' . $commandName . '](#' . $slug . ')';
//            }, $namespace[ 'commands' ])));
        }

        foreach ( $description->getCommands() as $command ) {
            $this->write("\n\n");
            $this->write($this->describeCommand($command));
        }
    }


    /**
     * {@inheritdoc}
     */
    protected function describeCommand(Command $command, array $options = [ ])
    {
        $command->getSynopsis();
        $command->mergeApplicationDefinition(false);

//        $this->write(
//            $command->getName()."\n"
//            .str_repeat('-', strlen($command->getName()))."\n\n"
//            .'* Description: '.($command->getDescription() ?: '<none>')."\n"
//            .'* Usage:'."\n\n"
//            .
//        );

        $usage = array_reduce(array_merge([ $command->getSynopsis() ], $command->getAliases(), $command->getUsages()), function ($carry, $usage) {
            return $carry .= '`' . $usage . '`' . "\n";
        });

        $this->write(
            $command->getName() . "\n"
            . str_repeat('-', strlen($command->getName())) . "\n\n"
        );

        if ( $help = $command->getProcessedHelp() ) {
            $this->write("\n");
            $this->write($help);
            $this->write("  \n\n");
            $this->write($usage);
        }

        if ( $command->getNativeDefinition() ) {
            $this->write("\n\n");
            $this->describeInputDefinition($command->getNativeDefinition());
        }
    }

}
