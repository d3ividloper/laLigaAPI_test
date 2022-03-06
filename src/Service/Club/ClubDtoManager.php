<?php

namespace App\Service\Club;

use App\Form\Model\ClubDto;

class ClubDtoManager
{
    public function create(): ClubDto
    {
        return new ClubDto();
    }
}
