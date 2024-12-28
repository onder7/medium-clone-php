<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı yapılandırması
$config['db'] = array(
    'host'     => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname'   => 'medium'
);

try {
    $db = new PDO(
        'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'] . ';charset=utf8mb4',
        $config['db']['username'],
        $config['db']['password'],
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        )
    );
} catch(PDOException $e) { 
    error_log("Database connection failed: " . $e->getMessage());
    die("Veritabanı bağlantısı kurulamadı.");
}

// Fonksiyon dosyalarını dahil et
$functions_path = dirname(__FILE__) . '/functions/';
require($functions_path . 'general.php');
require($functions_path . 'users.php');
require($functions_path . 'posthandler.php');

$current_file = explode('/', $_SERVER['SCRIPT_NAME']);
$current_file = end($current_file);

if (logged_in() === true) {
    $session_user_id = $_SESSION['user_id'];
    $user_data = user_data($session_user_id, 'user_id', 'username', 'password',
            'first_name', 'last_name', 'email', 'password_recover', 
            'type', 'allow_email', 'profile');

    if (user_active($user_data['username']) === false) {
        session_destroy();
        header('Location: index.php');
        exit();
    }
    
    if ($current_file !== 'changepassword.php' && $user_data['password_recover'] == 1) {
        header('Location: changepassword.php?force');
        exit();
    }
} else {
    $session_user_id = 0;
}

$errors = array();
?>