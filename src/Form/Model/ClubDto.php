<?php

namespace App\Form\Model;

class ClubDto {
    public $name;
    public $budget;
    public $base64Badge;
    public $coach;
    public $players;

    public function __constructor() {
        $this->players = [];
    }
}
