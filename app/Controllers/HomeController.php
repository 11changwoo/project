<?php

namespace App\Controllers;

use PDO;
use PDOException;
use App\Models\BoardModel;

class HomeController extends Controller {    

    public function index($request, $response, $args) {
        return $this->c->view->render($response, 'home.twig');
    }

    public function join($request, $response, $args) {
        return $this->c->view->render($response, 'join.twig');
    }

    public function login($request, $response, $args) {
        return $this->c->view->render($response, 'login.twig');
    }

    public function main($request, $response, $args) {        
        return $this->c->view->render($response, 'main.twig');
    }

    public function board($request, $response, $args) {

        $users = $this->c->db->query("select * from board")->fetchAll(PDO::FETCH_CLASS, BoardModel::class);

        return $this->c->view->render($response, 'board.twig', compact('users'));
    }

}