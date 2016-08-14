<?php

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
