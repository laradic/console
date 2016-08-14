<!--
title: Hide, disable & dev
subtitle: Commands
-->

# Hidden, excluded & dev

Using the `laradic.console` configuration, you can:
- Hide commands/groups from the `php artisan list`. Hidden commands can still be executed.
- Disable commands/groups entirely.
- Allow commands only when `app.debug` is on.

```php
/*
 * Namespaces: ['_global', 'app', 'auth', 'cache', 'config','db','event', 'key', 'schedule', 'route', 'session', 'vendor', 'view', 'migrate', 'queue', 'make']
 * Commands: ['cache:clear', 'etc...']
 */

return [
    // hide from list
    'hide'     => [
        'commands'   => [ ],
        'namespaces' => [ ],
    ],

    // disable commands
    'disable' => [
        'commands'   => [ ],
        'namespaces' => [ ],
    ],

    // disable commands when app.debug is false
    'debug' => [
        'commands'   => [ ],
        'namespaces' => [ ],
    ]
];
```
