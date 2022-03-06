<?php

namespace App\Service\Common;

use App\Entity\Club;
use App\Entity\ClubUser;
use App\Repository\ClubRepository;
use App\Repository\ClubUserRepository;
use App\Repository\PersonClubRepository;
use Doctrine\Persistence\ObjectManager;

class SalaryCalculator
{
    /**
     * @var PersonClubRepository
     */
    private $clubMemberRepository;
    /**
     * @var ClubRepository
     */
    private $clubRepository;

    public function __construct( PersonClubRepository $clubMemberRepository, ClubRepository $clubRepository)
    {
        $this->clubMemberRepository = $clubMemberRepository;
        $this->clubRepository = $clubRepository;
    }

    public function calculateFreeSalary($id)
    {
        $salary = $this->clubMemberRepository->getTotalSalary($id);
        $club = $this->clubRepository->find($id);
        return  $club->getBudget() - $salary;
    }

    public function getClubTotalSalary($id)
    {
        return $this->clubMemberRepository->getTotalSalary($id);
    }
}
