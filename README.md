<!---
title: Console 
author: Robin Radic
-->
Laradic Console
====================

[![Source](http://img.shields.io/badge/source-laradic/console-blue.svg?style=flat-square)](https://github.com/laradic/console)
[![License](http://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)
![Framework](http://img.shields.io/badge/framework-Laravel5-orange.svg?style=flat-square)

This package extends the default Laravel Console and adds several new features.

The package follows the FIG standards PSR-1, PSR-2, and PSR-4 to ensure a high level of interoperability between shared PHP code.

Installation
------------

```bash
composer require "laradic/console:~1.0"
```

Quick Overview
-------------
**Full documenation** @ [la.radic.nl](https://la.radic.nl)

There are 2 ways of using this package. 

#### Command only
Command only means you only use the `Laradic\Console\Command` class, which is an
extension of `Illuminate\Console\Command` with several improvements and additional features.
It is possible to use this class to extend your own Commands from without needing to register
the service provider or extend the Kernel.

Go to the [Command](docs/commands/command.md) documentation


#### Full features
Beside using the `Laradic\Console\Command` for your commands, to enable full features 
 it is required to extend your Console Kernel from the `Laradic\Console\Kernel` and 
 register the `Laradic\Console\ConsoleServiceProvider`.
   
In most cases this means editing the **app/Console/Kernel.php**:

```php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
// change
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
// to
use Laradic\Console\Kernel as ConsoleKernel; 

class Kernel extends ConsoleKernel {}
```

And registering the `Laradic\Console\ConsoleServiceProvider` inside **config/app.php**

