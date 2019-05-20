<?php

namespace App\Controllers;

use PDO;
use PDOException;

class UserController extends Controller {    

    public function insert($request, $response, $args) {

        $statement = $this->c->db->prepare("insert into user(user_id, password) values(:user_id, :password)");
        try {
            $statement -> execute([
                'user_id' => $request->getParam('user_id'),
                'password' => $request->getParam('password'),
            ]);
        } catch(PDOException $e) {
            return $response -> withStatus(400) -> write(json_encode([
                'error' => 'Already exist.'
            ]));
        }

        return $response->withRedirect($this->c->router->pathFor('home'));
    }


    public function login($request, $response, $args) {
        $statement = $this->c->db->prepare('select * from user where user_id = :id and password = :password');

        try {
            $statement -> execute([
                'id' => $request->getParam('user_id'), 
                'password' => $request->getParam('password')
            ]);

            if($statement->rowCount() != 0) {
                $this->c->session->set('id',$request->getParam('user_id'));

                return $response->withRedirect($this->c->router->pathFor('main'));
            }
        } catch(PDOException $e) {
            return $response -> withStatus(400);
        }        

        return $response->withRedirect($this->c->router->pathFor('login'));
    }


    public function modify($request, $response, $args) {
        $session = $this->c->session->id;

        if($session !== null) {
            $statement = $this->c->db->prepare("select * from user where user_id = :id");
            $statement->execute(['id' => $session]);

            $response->withHeader('Content-Type', 'application/json');
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            return $this->c->view->render($response, 'modify.twig', [
                'users' => $user,
                'json' => json_encode($user)
            ]);
        } else {
            return $response->withRedirect($this->c->router->pathFor('login'));
        }
    }
    
    public function update($request, $response, $args) {

        $statement = $this->c->db->prepare("update user set user_name = :user_name where user_id = :id");

        try {
            $statement->execute([
                'user_name' => $request->getParam('user_name'),
                'id' => $request->getParam('user_id')
            ]);
        } catch(PDOException $e) {
            return $response->withStatus(400);
        }

        return $response->withRedirect($this->c->router->pathFor('main'));
    }    

}