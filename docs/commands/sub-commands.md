<!--
title: Sub-commands
subtitle: Commands
-->

# Sub-commands
Sub commands can best be explained by just showing it. This example shows a custom application
which implements sub-commands quite a bit.

`php artisan` or `php artisan list`


![php artisan](http://i.imgur.com/YK2K9ib.png)
 
 
`php artisan remotes` fires `RemotesCommand`


![php artisan remotes](http://i.imgur.com/wXSeSCV.png)


`php artisan remotes list` fires `RemotesListCommand`


![php artisan remotes list](http://i.imgur.com/KgR7eiC.png)
 
**RemotesCommand** class
```php
class RemotesCommand extends Command
{
    use ProvidesSubCommands;

    protected $title = 'Remotes';
    protected $signature = 'remotes {command-name?} {args?*}';
    protected $description = 'Connection manager for VCS providers (Github/Bitbucket)';
    protected $commands = [
        RemotesCreateCommand::class,
        RemotesDeleteCommand::class,
        RemotesDefaultCommand::class,
        RemotesEditCommand::class,
        RemotesInfoCommand::class,
        RemotesListCommand::class
    ];
}
```

**RemotesListCommand**
```php
class RemotesListCommand extends Command
{
    protected $signature = 'remotes:list';
    protected $description = 'List all remotes';

    public function handle()
    {
        $this->table(); 
        // ....
    }
```
