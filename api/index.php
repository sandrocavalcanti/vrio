<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->add(new Slim\Middleware\SessionCookie(array('secret' => 'appvrio')));

// Load all our routes
require './routes/main.php';
require './routes/customer.php';
require './routes/banheiro.php';
require './routes/user.php';
require './routes/produto.php';
 
$app->run();