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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class GlobalCommand extends Command
{
    const NAME = 'global';

  #  protected $enabled = false;

    protected $description = 'Allows running commands in the global mode';

    /**
     * GlobalCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()

    {
        $this
            ->setName(static::NAME)
            ->setDescription('Allows running commands in the global mode.')
            ->setDefinition(array(
                new InputArgument('command-name', InputArgument::REQUIRED, ''),
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
            ))
            ->setHelp(<<<EOT
Use this command as a wrapper to run other Composer commands
within the global context of COMPOSER_HOME.
You can use this to install CLI utilities globally, all you need
is to add the COMPOSER_HOME/vendor/bin dir to your PATH env var.
COMPOSER_HOME is c:\Users\<user>\AppData\Roaming\Composer on Windows
and /home/<user>/.composer on unix systems.
Note: This path may vary depending on customizations to bin-dir in
composer.json or the environmental variable COMPOSER_BIN_DIR.
EOT
            )
        ;
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
        $this->modes()->enable('global');

        // extract real command name
        $tokens = preg_split('{\s+}', $input->__toString());
        $args = array();
        foreach ($tokens as $token) {
            if ($token && $token[0] !== '-') {
                $args[] = $token;
                if (count($args) >= 2) {
                    break;
                }
            }
        }
        // show help for this command if no command was found
        if (count($args) < 2) {
            return parent::run($input, $output);
        }
        $input = new StringInput(preg_replace('{\bg(?:l(?:o(?:b(?:a(?:l)?)?)?)?)?\b}', '', (string) $input, 1));
        $code = $this->getApplication()->run($input, $output);
        if($this->modes()->isEnabled('global') && ($this->isDebug() || config('app.debug', false))){
            $output->write('<global-enabled>Global enabled</global-enabled>');
        }

        return $code;
    }

}
