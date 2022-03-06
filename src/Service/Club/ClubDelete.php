<?php

namespace App\Service\Club;

use App\Repository\ClubRepository;

class ClubDelete
{
    /**
     * @var ClubRepository
     */
    private $clubRepository;

    public function __construct(ClubRepository $clubRepository)
    {
        $this->clubRepository = $clubRepository;
    }

    public function __invoke(string $id)
    {
        $club = $this->clubRepository->find($id);
        $this->clubRepository->remove($club);
    }
}
