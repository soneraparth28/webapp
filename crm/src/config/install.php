<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Server Requirements
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel server requirements, you can add as many
    | as your application require, we check if the extension is enabled
    | by looping through the array and run "extension_loaded" on it.
    |
    */
    'core' => [
        'min_php_version' => '7.4.0',
    ],

    /*
    |-------------------------------------------------------------------------
    | Determine if the app is installed or not
    |-------------------------------------------------------------------------
    */
    'app_installed' => env('APP_INSTALLED', true),

    /*
     |-----------------------------------------------------------------------
     | If app is already installed
     |-----------------------------------------------------------------------
     */

    'installed' => [
        'redirect_options' => [
            'route' => [
                'name' => 'users.login.index',
                'data' => [],
            ],
            'abort' => [
                'type' => '404',
            ],
            'dump' => [
                'data' => 'Dumping a not found message.',
            ],
        ],
    ],

    /*
   |--------------------------------------------------------------------------
   | Folders Permissions
   |--------------------------------------------------------------------------
   |
   | This is the default Laravel folders permissions, if your application
   | requires more permissions just add them to the array list bellow.
   |
   */
    'permissions' => [
        'storage/'           => '775',
        'bootstrap/cache/'       => '775',
        '.env'       => '777',
    ],

    'requirements' => [
        'php' => [
            'openssl',
            'pdo',
            'mbstring',
            'tokenizer',
            'JSON',
            'cURL',
            'bcMath',
            'fileinfo',
            'XML',
            'ctype'
        ],
        'apache' => [
            'mod_rewrite',
        ],
    ],

    'final' => [
        'key' => true,
        'publish' => false
    ]
];
