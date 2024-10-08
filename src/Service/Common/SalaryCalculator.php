<?php

namespace App\Service\Common;

use App\Repository\ClubRepository;
use App\Repository\PersonClubRepository;
use Doctrine\ORM\NonUniqueResultException;

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

    /**
     * @throws NonUniqueResultException
     */
    public function calculateFreeSalary($id)
    {
        $salary = $this->clubMemberRepository->getTotalSalary($id);
        $club = $this->clubRepository->find($id);
        return  $club->getBudget() - $salary;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getClubTotalSalary($id)
    {
        return $this->clubMemberRepository->getTotalSalary($id);
    }
}
