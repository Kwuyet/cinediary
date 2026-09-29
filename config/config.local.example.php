<?php
// Copy this file to config.local.php and fill in your own values.
// config.local.php is listed in .gitignore, so your passwords never reach GitHub.
return [
    'base_path' => '',            // '/CineDiary' if you open the site at http://localhost/CineDiary
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'cinediary',
        'user' => 'root',
        'pass' => '',
        'port' => 3306,
    ],
    'auth' => [
        'google_client_id' => null,
        'google_client_secret' => null,
    ],
    'mail' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => '',
        'password' => '',          // Gmail App Password
        'encryption' => 'tls',
        'from' => ['address' => '', 'name' => 'CineDiary'],
    ],
];
