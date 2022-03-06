<?php

namespace App\Service\PersonClub;

use App\Entity\Club;
use App\Entity\ClubUser;
use App\Entity\PersonClub;
use App\Form\Model\ClubDto;
use App\Form\Model\ClubPlayerDto;
use App\Form\Type\ClubUserFormType;
use App\Form\Type\PersonClubFormType;
use App\Form\Type\PlayerFormType;
use App\Repository\ClubUserRepository;
use App\Repository\PersonClubRepository;
use App\Service\Club\ClubManager;
use App\Service\Common\NotificationManager;
use App\Service\Common\SalaryCalculator;
use App\Service\Person\PersonManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ClubCoachFormProcessor
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
    private $salaryCalculator;
    /**
     * @var PersonManager
     */
    private $userManager;
    /**
     * @var PersonClubRepository
     */
    private $clubUserRepository;

    /** @var NotificationManager */
    private $notificationManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        ClubManager $clubManager,
        PersonManager $userManager,
        PersonClubRepository $clubUserRepository,
        SalaryCalculator $salaryCalculator,
        ClubMemberManager $clubMemberManager,
        NotificationManager $notificationManager
    )
    {
        $this->clubMemberManager = $clubMemberManager;
        $this->formFactory = $formFactory;
        $this->clubManager = $clubManager;
        $this->salaryCalculator = $salaryCalculator;
        $this->userManager = $userManager;
        $this->clubUserRepository = $clubUserRepository;
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
                return [null, "Selected coach is already asigned to this club"];
            if (!empty($existAtAnotherClub))
                return [null, "Selected coach is already asigned to another club"];
            // check salary
            $freeSalary = $this->salaryCalculator->calculateFreeSalary($clubId);
            if ($content["salary"] > $freeSalary)
                return [ null, 'The club does not have enough budget to hire selected coach' ];

            $club = $this->clubManager->find($clubId);
            $user = $this->userManager->find($content["id_user"]);
            $clubUser->setPerson($user);
            $clubUser->setClub($club);
            $clubUser->setSalary($content["salary"]);
            $clubUser->setType(clubMemberManager::TYPE_COACH);
            $coach = $this->clubUserRepository->getCoach($clubId);
            // When exist coach, change by new coach
            if ($coach)
                $this->clubUserRepository->delete($coach);

            $this->clubMemberManager->save($clubUser);
            // Send email notification
            // ToDO: it would be better send notification after a domain event
            $this->notificationManager->sendNotification($club, $clubUser);
            return [ $clubUser, null ];
        }
        return [ null , "Invalid form" ];
    }
}
