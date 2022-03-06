<?php

namespace App\Service\PersonClub;

use App\Repository\ClubRepository;
use App\Repository\PersonClubRepository;
use App\Service\Common\NotificationManager;

class ClubMemberDelete
{

    /**
     * @var PersonClubRepository
     */
    private $clubRepository;

    /** @var NotificationManager */
    private $notificationManager;

    public function __construct(
        PersonClubRepository $clubRepository,
        NotificationManager $notificationManager)
    {

        $this->clubRepository = $clubRepository;
        $this->notificationManager = $notificationManager;
    }

    public function __invoke(string $id)
    {
        $clubMember = $this->clubRepository->find($id);
        $this->clubRepository->delete($clubMember);
        // Send email notification
        // ToDO: it would be better send notification after a domain event
        $this->notificationManager->sendRemoveNotification($clubMember->getClub(), $clubMember);
    }
}
