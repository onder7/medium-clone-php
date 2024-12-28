<?php
$config['db'] = array(
    'host'     => 'localhost',
    'username' => 'your_db_username',
    'password' => 'your_db_password',
    'dbname'   => 'medium'
);

// Email configuration
$config['email'] = array(
    'from_address' => 'noreply@yourdomain.com',
    'from_name'    => 'Broomble'
);

// Site configuration
$config['site'] = array(
    'url'      => 'https://yourdomain.com',
    'name'     => 'Broomble',
    'timezone' => 'UTC'
);

// Security configuration
$config['security'] = array(
    'salt'     => 'your_random_salt_here',
    'session_timeout' => 3600 // 1 hour
);
?>