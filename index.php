<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start(); // ← nutné pro přístup k $_SESSION

mb_internal_encoding("utf8");

require_once __DIR__ . '/vendor/autoload.php';


spl_autoload_register(function ($class) {
    $class = ltrim($class, '\\');

    // Pokud třída začíná na base\, mapujeme na modules/base/
    if (str_starts_with($class, 'base\\')) {
        $relative = substr($class, strlen('base\\'));
        $path = __DIR__ . '/modules/base/' . str_replace('\\', '/', $relative) . '.php';
    } else {
        $path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    }

    if (file_exists($path)) {
        require_once $path;
    } else {
        error_log("Autoloader: Soubor {$path} neexistuje pro třídu {$class}.");
    }
});







/*  
// Použití
//
// $user = new modul1\model\User();
// $controller = new modul1\controller\UserController();
//
*/

$router = new base\controller\RouterController();
$router->process(array($_SERVER['REQUEST_URI']));