<?php

namespace App\Controllers;

use PDO;
use PDOException;

use App\Models\BoardModel;

class BoardController extends Controller {

    public function write($request, $response, $args) {
        $session = $this->c->session->id;

        if($session !== null) {
            return $this->c->view->render($response, 'write.twig');
        } else {
            return $response->withRedirect($this->c->router->pathFor('login'));   
        }
    }

    public function update($request, $response, $args) {

        $user_id = $this->c->session->get('id');
        $params = $request->getParams();

        $statement;

        if($params['no'] === '') {
            $statement = $this->c->db->prepare("insert into board(user_id, subject, content) values(:user_id, :subject, :content)");    
        } else {
            $statement = $this->c->db->prepare("update board set subject = :subject, content = :content where no = :no");
        }
        

        try {
            if($params['no'] === '') {
                $statement->execute([
                    'user_id' => $user_id,
                    'subject' => $params['subject'],
                    'content' => $params['content']
                ]);    
            } else {
                $statement->execute([
                    'no' => $params['no'],
                    'subject' => $params['subject'],
                    'content' => $params['content']
                ]);
            }
            


        } catch(PDOException $e) {
            return $response->withStatus(400);
        }

        return $response->withRedirect($this->c->router->pathFor('board'));
    }

    public function view($request, $response, $args) {
        $session = $this->c->session->id;

        $no = $request->getParam('no');
        $board = $this->c->db->query('select * from board where no = '.$no)->fetchAll(PDO::FETCH_CLASS, BoardModel::class);

        return $this->c->view->render($response, 'view.twig', [
            'board' => $board,
            'session' => $session
        ]);
    }

    public function modify($request, $response, $args) {
        $no = $request->getParam('no');
        $board = $this->c->db->query('select * from board where no = '.$no)->fetchAll(PDO::FETCH_CLASS, BoardModel::class);

        return $this->c->view->render($response, 'write.twig', [
            'board' => $board
        ]);
    }

    public function delete($request, $response, $args) {
        $no = $request->getParam('no');
        $board = $this->c->db->query('delete from board where no = '.$no);

        return $response->withRedirect($this->c->router->pathFor('board'));
    }
}