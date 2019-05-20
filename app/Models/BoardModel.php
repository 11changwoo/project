<?php

namespace App\Models;

class BoardModel {

    public function getNo() {
        return $this->no;
    }

    public function getUser_id() {
        return $this->user_id;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getContent() {
        return $this->content;
    }

    public function getCreate_datetime() {
        return $this->create_datetime;
    }
}