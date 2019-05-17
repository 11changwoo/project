<?php

namespace App\Controllers;

use PDO;
use PDOException;

class UserController extends Controller {
    // //전체조회
    // public function index($request, $response, $args) {
    //     $users = $this->c->db->query("select * from user")->fetchAll(PDO::FETCH_ASSOC);

    //     return $response->withJson($users);
    // }
    // //아이디 중복 검사[?]
    // public function show($request, $response, $args) {
    //     $user = $this->getUserById($args['id']);

    //     if($user === null) {
    //         return $response->widhStatus(404);
    //     }

    //     return $response->withJson($user);
    // }
    // //db insert
    // public function store($request, $response, $args) {
    //     $statement = $this->c->db->prepare("insert into user(user_id, password) values(:user_id, :password)");
    //     try {
    //         $statement -> execute([
    //             'user_id' => $request->getParam('user_id'),
    //             'password' => $request->getParam('password'),
    //         ]);
    //     } catch(PDOException $e) {
    //         return $response->withStatue(400);
    //     }

    //     return $response->withJson($this->getUserById($request->getParam('user_id')));
    // }
    // //db update
    // public function update($request, $response, $args) {
    //     $params = $request->getParams();        

    //     $sqlParams = implode(', ', array_map(function($column) {
    //         return $column . ' = :' . $column;
    //     }, array_keys($params)));        

    //     $statement = $this->c->db->prepare("update user set $sqlParams where user_id = :id");

    //     try {
    //         $statement->execute(array_merge($params,['id' => $args['id']]));
    //     } catch(PDOException $e) {
    //         return $response->withStatus(400);
    //     }

    //     return $response->withJson($this->getUserById($args['id']));

    // }
    // //db delete
    // public function destroy($request, $response, $args) {

    //     if($this->getUserById($args['id']) === null) {
    //         return $response -> withStatus(404);
    //         // return $response -> withStatus(404) -> write(json_encode([
    //         //     'error' => 'User does not exist.'
    //         // ]));
    //     }

    //     $statement = $this->c->db->prepare("delete from user where user_id = :id");

    //     try {
    //         $statement -> execute(['id' => $args['id']]);
    //     } catch(PDOException $e) {
    //         return $response->withStatus(400);
    //     }

    //     return $response->withStatus(204);
    // }
    //아이디 중복 검사
    // protected function getUserById($id) {
    //     $statement = $this->c->db->prepare("select * from user where user_id = :id");
    //     $statement->execute(['id' => $id]);

    //     if($statement->rowCount() === 0) {
    //         return null;
    //     }

    //     return $statement->fetch(PDO::FETCH_ASSOC);
    // }


    //---------
    //회원가입
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

        // return $response->withJson($this->getUserById($request->getParam('user_id')));
        return $response->withRedirect($this->c->router->pathFor('home'));
    }
    //정보수정
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

    //로그인
    // public function login($request, $response, $args) {
        
    //     $statement = $this->c->db->prepare("select * from user where user_id = :id and password = :password");

    //     try {
    //         $statement -> execute([
    //             'id' => $request->getParam('user_id'), 
    //             'password' => $request->getParam('password')
    //         ]);

    //         if($statement->rowCount() != 0) {
    //             $_SESSION['id'] = $request->getParam('user_id');

    //             return $response->withRedirect($this->c->router->pathFor('board'));
    //         }
    //     } catch(PDOException $e) {
    //         return $response -> withStatus(400);
    //     }        

    //     return $response->withRedirect($this->c->router->pathFor('login'));
    // }

}