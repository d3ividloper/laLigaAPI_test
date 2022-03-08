<?php

namespace App\Tests\Service;


use App\Entity\Club;
use App\Entity\Person;
use App\Entity\PersonClub;
use App\Form\Model\ClubPlayerDto;
use App\Service\Club\ClubManager;
use App\Service\Common\NotificationManager;
use App\Service\Common\SalaryCalculator;
use App\Service\Person\PersonManager;
use App\Service\PersonClub\ClubMemberManager;
use App\Service\PersonClub\PersonClubFormProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class PersonClubFormProcessorTest extends TestCase
{

    public function testSuccess()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $form = $this->createMock(FormInterface::class);
        $clubManager = $this->createMock(ClubManager::class);
        $personManager = $this->createMock(PersonManager::class);
        $notificationManager = $this->createMock(NotificationManager::class);
        $salaryCalculator = $this->createMock(SalaryCalculator::class);
        $clubMemberManager = $this->createMock(ClubMemberManager::class);

        $club = new Club();
        $club->setBudget(1000);
        $person = new Person();
        $personClub = new PersonClub();

        $clubPlayerDto = new ClubPlayerDto();
        $clubPlayerDto->id_user = '1';
        $clubPlayerDto->salary = 200;

        $salaryCalculator->expects($this->any())
            ->method('calculateFreeSalary')
            ->willReturn(800);

        $clubManager->expects($this->any())
            ->method('find')
            ->willReturn($club);

        $clubManager->expects($this->any())
            ->method('save')
            ->willReturn($club);

        $clubMemberManager->expects($this->any())
            ->method('save')
            ->willReturn($personClub);

        $personManager->expects($this->any())
            ->method('find')
            ->willReturn($person);

        $form->expects($this->any())
            ->method('isValid')
            ->willReturn(true);

        $form->expects($this->any())
            ->method('isSubmitted')
            ->willReturn(true);

        $formFactory->expects($this->any())
            ->method('create')
            ->willReturn($form);

        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(json_encode(['salary' => 20, 'id_user' => 1])));
        $formProcessor = new PersonClubFormProcessor($formFactory, $clubManager, $personManager, $notificationManager, $salaryCalculator, $clubMemberManager);
        $response = $formProcessor($request, 1);
        $this->assertEquals([$personClub, null], $response);
    }

    public function testPlayerAlreadyExist()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $form = $this->createMock(FormInterface::class);
        $clubManager = $this->createMock(ClubManager::class);
        $personManager = $this->createMock(PersonManager::class);
        $notificationManager = $this->createMock(NotificationManager::class);
        $salaryCalculator = $this->createMock(SalaryCalculator::class);
        $clubMemberManager = $this->createMock(ClubMemberManager::class);

        $club = new Club();
        $club->setBudget(1000);
        $person = new Person();
        $personClub = new PersonClub();

        $clubPlayerDto = new ClubPlayerDto();
        $clubPlayerDto->id_user = '1';
        $clubPlayerDto->salary = 200;

        $form->expects($this->any())
            ->method('isValid')
            ->willReturn(true);

        $form->expects($this->any())
            ->method('isSubmitted')
            ->willReturn(true);

        $formFactory->expects($this->any())
            ->method('create')
            ->willReturn($form);

        $clubMemberManager->expects($this->any())
            ->method('findBy')
            ->willReturn([$personClub]);

        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(json_encode(['salary' => 20, 'id_user' => 1])));
        $formProcessor = new PersonClubFormProcessor($formFactory, $clubManager, $personManager, $notificationManager, $salaryCalculator, $clubMemberManager);
        $response = $formProcessor($request, 1);
        $this->assertEquals([null, "Selected player is already asigned to this club"], $response);
    }

    public function testNotEnoughClubBudgetToHirePlayer()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $form = $this->createMock(FormInterface::class);
        $clubManager = $this->createMock(ClubManager::class);
        $personManager = $this->createMock(PersonManager::class);
        $notificationManager = $this->createMock(NotificationManager::class);
        $salaryCalculator = $this->createMock(SalaryCalculator::class);
        $clubMemberManager = $this->createMock(ClubMemberManager::class);

        $club = new Club();
        $club->setBudget(1000);
        $personClub = new PersonClub();
        $personClub->setSalary(2000);

        $clubManager->expects($this->any())
            ->method('find')
            ->willReturn($club);

        $clubMemberManager->expects($this->any())
            ->method('find')
            ->willReturn($personClub);

        $form->expects($this->any())
            ->method('isValid')
            ->willReturn(true);

        $form->expects($this->any())
            ->method('isSubmitted')
            ->willReturn(true);

        $formFactory->expects($this->any())
            ->method('create')
            ->willReturn($form);

        $clubMemberManager->expects($this->any())
            ->method('findBy')
            ->willReturn([]);

        $salaryCalculator->expects($this->any())
            ->method('calculateFreeSalary')
            ->willReturn(-1000);

        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(json_encode(['salary' => 20, 'id_user' => 1])));
        $formProcessor = new PersonClubFormProcessor($formFactory, $clubManager, $personManager, $notificationManager, $salaryCalculator, $clubMemberManager);
        $response = $formProcessor($request, 1);
        $this->assertEquals([null, 'The club does not have enough budget to hire selected player'], $response);
    }
}
