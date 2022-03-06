<?php

namespace App\Service\Coach;

use App\Form\Model\CoachDto;

class CoachDtoManager
{
    public function create(): CoachDto
    {
        return new CoachDto();
    }
}
