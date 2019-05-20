<?php

use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\BoardController;

session_start();

require 'vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

$app -> add(new \Anddye\Middleware\SessionMiddleware([
    'name' => 'slim_session',
    'autorefresh' => true,
    'lifetime' => '1 hour'
]));

$container = $app->getContainer();

$container['session'] = function($container) {
    return new \Anddye\Session\Helper();
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__.'/resources/views', [
        'cache' => false,
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};

$container['db'] = function() {
    $db =  new PDO('mysql:host=localhost;dbname=project','root','123456');
    $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $db;
};

$app->get('/', HomeController::class.':index')->setName('home');


$app->group('/user', function() {
    $this->get('/join', HomeController::class . ':join');
    $this->post('/insert', UserController::class . ':insert');    
    $this->post('/update', UserController::class . ':update');  
});


$app->group('/login', function() {
    $this->get('', HomeController::class . ':login')->setName('login');
    $this->post('/connect', UserController::class . ':login');
});


$app->group('/main', function() {
    $this->get('', HomeController::class . ':main')->setName('main');
    $this->get('/modify', UserController::class . ':modify');
});


$app->group('/board', function() {
    $this->get('', HomeController::class . ':board')->setName('board');
    $this->get('/write', BoardController::class . ':write');
    $this->post('/update', BoardController::class . ':update');
    $this->get('/view', BoardController::class . ':view');
    $this->get('/modify', BoardController::class . ':modify');
    $this->get('/delete', BoardController::class . ':delete');
});

$app->run();