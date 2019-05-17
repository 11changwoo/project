<?php

namespace App\Controllers;

use PDO;
use PDOException;

class BoardController extends Controller {
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
}