<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */
/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('rejestracja', ['controller' => 'Signup', 'action' => 'new']);
$router->add('wydatki', ['controller' => 'Expenses', 'action' => 'new']);
$router->add('przychody', ['controller' => 'Incomes', 'action' => 'new']);
$router->add('bilans', ['controller' => 'Balance', 'action' => 'new']);
$router->add('ustawienia', ['controller' => 'Settings', 'action' => 'new']);
$router->add('{controller}/{action}');
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
    
$router->dispatch($_SERVER['QUERY_STRING']);
