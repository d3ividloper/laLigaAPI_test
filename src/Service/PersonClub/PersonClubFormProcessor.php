<?php

namespace App\Service\PersonClub;

use App\Entity\PersonClub;
use App\Form\Model\ClubPlayerDto;
use App\Form\Type\PersonClubFormType;
use App\Service\Club\ClubManager;
use App\Service\Common\NotificationManager;
use App\Service\Common\SalaryCalculator;
use App\Service\Person\PersonManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PersonClubFormProcessor
{
    /**
     * @var ClubMemberManager
     */
    private $clubMemberManager;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var ClubManager
     */
    private $clubManager;
    /**
     * @var SalaryCalculator
     */
    private $salaryLimitCalculator;

    /**
     * @var PersonManager
     */
    private $userManager;
    /**
     * @var NotificationManager
     */
    private $notificationManager;

    public function __construct(
        FormFactoryInterface  $formFactory,
        ClubManager $clubManager,
        PersonManager $userManager,
        NotificationManager $notificationManager,
        SalaryCalculator $salaryLimitCalculator,
        ClubMemberManager $clubMemberManager
    )
    {
        $this->clubMemberManager = $clubMemberManager;
        $this->formFactory = $formFactory;
        $this->clubManager = $clubManager;
        $this->salaryLimitCalculator = $salaryLimitCalculator;
        $this->userManager = $userManager;
        $this->notificationManager = $notificationManager;
    }

    public function __invoke(Request $request, string $clubId = null): array
    {
        $clubUser = $this->clubMemberManager->create();
        $content = json_decode($request->getContent(), true);
        $dto = new ClubPlayerDto();
        $form = $this->formFactory->create(PersonClubFormType::class, $dto);
        $form->submit($content);
        if ($form->isSubmitted() && $form->isValid()) {
            // check existing
            $existAtClub = $this->clubMemberManager->findBy(['person' => $content["id_user"], 'club' => $clubId]);
            $existAtAnotherClub = $this->clubMemberManager->findBy(['person' => $content["id_user"]]);
            if (!empty($existAtClub))
                return [null, "Selected player is already asigned to this club"];
            if (!empty($existAtAnotherClub))
                return [null, "Selected player is already asigned to another club"];

            // check salary
            $freeSalary = $this->salaryLimitCalculator->calculateFreeSalary($clubId);
            if ($content["salary"] > $freeSalary)
                return [ null, 'The club does not have enough budget to hire selected player' ];

            $club = $this->clubManager->find($clubId);
            $user = $this->userManager->find($content["id_user"] );
            // Set data
            $clubUser->setPerson($user);
            $clubUser->setClub($club);
            $clubUser->setSalary($content["salary"]);
            $clubUser->setType(clubMemberManager::TYPE_PLAYER);
            $clubUser=$this->clubMemberManager->save($clubUser);
            // Send email notification
            // ToDO: it would be better send notification after a domain event
            $this->notificationManager->sendNotification($club, $clubUser);
            return [ $clubUser, null ];
        }
        return [ null , "Invalid form" ];
    }
}
