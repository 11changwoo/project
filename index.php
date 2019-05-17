<?php

use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\BoardController;
use App\Models\Board;

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

// $app->group('/users',function() {
//     $this->get('', UserController::class .':index');
//     $this->get('/{id}', UserController::class .':show');
//     $this->post('', UserController::class .':store');
//     $this->map(['PUT', 'PATCH'], '/{id}', UserController::class.':update');
//     $this->delete('/{id}', UserController::class .':destroy');
// });

//회원가입 route
$app->group('/user', function() {
    $this->get('/join', HomeController::class . ':join');
    $this->post('/insert', UserController::class . ':insert');    
    $this->post('/update', UserController::class . ':update');  
});

//로그인
$app->group('/login', function() {
    $this->get('', HomeController::class . ':login')->setName('login');
    // $this->post('/connect', UserController::class . ':login');
    $this->post('/connect', function($request, $response) {
        $statement = $this->db->prepare('select * from user where user_id = :id and password = :password');

        try {
            $statement -> execute([
                'id' => $request->getParam('user_id'), 
                'password' => $request->getParam('password')
            ]);

            if($statement->rowCount() != 0) {
                $this->session->set('id',$request->getParam('user_id'));

                return $response->withRedirect($this->router->pathFor('main'));
            }
        } catch(PDOException $e) {
            return $response -> withStatus(400);
        }        

        return $response->withRedirect($this->router->pathFor('login'));
    });  
});

//main
$app->group('/main', function() {
    $this->get('', HomeController::class . ':main')->setName('main');
    $this->get('/modify', function($request, $response) {
        // $response->getBody()->write($this->session->get('id'));
        // $this->session->delete('id');
        $session = $this->session->id;

        if($session !== null) {
            $statement = $this->db->prepare("select * from user where user_id = :id");
            $statement->execute(['id' => $session]);

            $response->withHeader('Content-Type', 'application/json');
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            return $this->view->render($response, 'modify.twig', [
                'users' => $user,
                'json' => json_encode($user)
            ]);
        } else {
            return $response->withRedirect($this->router->pathFor('login'));
        }
    });

});

//board
$app->group('/board', function() {
    $this->get('', HomeController::class . ':board')->setName('board');
    $this->get('/write', function($request, $response) {
        $session = $this->session->id;

        if($session !== null) {
            return $this->view->render($response, 'write.twig');
        } else {
            return $response->withRedirect($this->router->pathFor('login'));   
        }
    });
    $this->post('/update', BoardController::class . ':update');
    $this->get('/view', function($request, $response) {
        $session = $this->session->id;

        $no = $request->getParam('no');
        $board = $this->db->query('select * from board where no = '.$no)->fetchAll(PDO::FETCH_CLASS, Board::class);

        return $this->view->render($response, 'view.twig', [
            'board' => $board,
            'session' => $session
        ]);
        
    });
    $this->get('/modify', function($request, $response) {
        $no = $request->getParam('no');
        $board = $this->db->query('select * from board where no = '.$no)->fetchAll(PDO::FETCH_CLASS, Board::class);

        return $this->view->render($response, 'write.twig', [
            'board' => $board
        ]);
    });
    $this->get('/delete', function($request, $response) {
        $no = $request->getParam('no');
        $board = $this->db->query('delete from board where no = '.$no);

        return $response->withRedirect($this->router->pathFor('board'));
    });
});

$app->run();