<?php

namespace App\Service\Player;

use App\Form\Model\PlayerDto;

class PlayerDtoManager
{
    public function create(): PlayerDto
    {
        return new PlayerDto();
    }
}
