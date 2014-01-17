<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Recife');

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->add(new Slim\Middleware\SessionCookie(array('secret' => 'appvrio')));

$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['vrio']['auth'])) {
            echo '{"auth":false}';
            exit;
        }
    };
};

// Load all our routes
require './routes/main.php';
require './routes/customer.php';
require './routes/banheiro.php';
require './routes/user.php';
require './routes/produto.php';
require './routes/venda.php';
require './routes/ponto.php';
 
$app->run();