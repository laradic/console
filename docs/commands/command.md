<!--
title: Command
subtitle: Commands
-->

# Command

[`Laradic\Console\Command`](#phpdoc:popover:Laradic\Console\Command) extends [`Illuminate\Console\Command`](#phpdoc:popover:Illuminate\Console\Command) and adds extra functions.


To use it:

```php
class MyCommand extends \Laradic\Console\Command
{
    protected $signature = '';
    public function handle(){
        // same as normal
    }
}
```

### Added functions
```php

// output shortcuts
$this->output->writeln($text); // old way
$this->writeln($text); // new way

$this->write($text);
$this->title($text); 
$this->section($text);
$this->note($text);
$this->success($text);
$this->warning($text);
$this->caution($text);
$this->listing([ 'first', 'second' ]);

$this->isEnabled();
$this->enable();
$this->disable();

$this->hasOption($name);
$this->hasArgument($name);

$this->askArgument($key, $question = null, $default = null);
$this->askSecretArgument($key, $question = null, $default = null);
$this->askChoiceArgument($key, (array) $choices, $question = null, $default = null, $multi = false);

$this->hasRootAccess();
$this->dump($args);

```



### Extra features
These functions/features only work if you implemented the `Laradic\Console\Kernel` class as described [here](../index.md)

These features work with helpers.
```
$this->writeln($this->style(['bold', 'yellow'], 'text to show'));
$this->writeln($this->style('bold', 'yellow', 'This also works'));
$this->tree($arr);
```

