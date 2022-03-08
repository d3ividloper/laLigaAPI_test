<?php

namespace App\Tests\Service;

use App\Entity\Club;
use App\Entity\PersonClub;
use App\Repository\ClubRepository;
use App\Repository\PersonClubRepository;
use App\Service\Common\SalaryCalculator;
use PHPUnit\Framework\TestCase;

class SalaryCalculatorTest extends TestCase
{
    public function testSuccessFreeSalary()
    {
        $clubMember = new PersonClub();
        $clubMember->setSalary(1000.00);
        $club = new Club();
        $club->setBudget(5000.00);
        $club->setName("ClubTest");

        $clubMemberRepo = $this->createMock(PersonClubRepository::class);
        $clubRepo = $this->createMock(ClubRepository::class);

        $clubMemberRepo->expects($this->any())
            ->method('getTotalSalary')
            ->willReturn($clubMember->getSalary());
        $clubRepo->expects($this->any())
            ->method('find')
            ->willReturn($club);

        $salaryCalculator = new SalaryCalculator($clubMemberRepo, $clubRepo);
        $this->assertEquals(4000, $salaryCalculator->calculateFreeSalary(1));
    }

    public function testNegativeFreeSalary() {
        $clubMember = new PersonClub();
        $clubMember->setSalary(2000);
        $club = new Club();
        $club->setBudget(1000);

        $clubMemberRepo = $this->createMock(PersonClubRepository::class);
        $clubRepo = $this->createMock(ClubRepository::class);

        $clubMemberRepo->expects($this->any())
            ->method('getTotalSalary')
            ->willReturn($clubMember->getSalary());
        $clubRepo->expects($this->any())
            ->method('find')
            ->willReturn($club);

        $salaryCalculator = new SalaryCalculator($clubMemberRepo, $clubRepo);
        $this->assertEquals(-1000, $salaryCalculator->calculateFreeSalary(1));
    }
}
