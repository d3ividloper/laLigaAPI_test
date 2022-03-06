<?php

namespace App\Service\Coach;

use App\Form\Model\CoachDto;
use App\Form\Type\CoachFormType;
use App\Service\Common\NotificationManager;
use App\Service\Person\PersonManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CoachFormProcessor
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var PersonManager
     */
    private $userManager;
    /**
     * @var CoachManager
     */
    private $coachManager;

    /** @var coachDtoManager  */
    private $coachDtoManager;

    /**
     * @var NotificationManager
     */
    private $notificationManager;

    public function __construct(FormFactoryInterface $formFactory, PersonManager $userManager, CoachManager $coachManager, CoachDtoManager $coachDtoManager, NotificationManager $notificationManager)
    {
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->coachManager = $coachManager;
        $this->coachDtoManager = $coachDtoManager;
        $this->notificationManager = $notificationManager;
    }

    public function __invoke(Request $request, ?string $userId = null): array
    {
        $coachDto = $this->coachDtoManager->create();
        if ($userId != null) {
            $coach = $this->coachManager->find($userId);
            $user = $coach->getPerson();

        }else {
            $user = $this->userManager->create();
            $coach = $this->coachManager->create();
        }

        $content = json_decode($request->getContent(), true);
        $form = $this->formFactory->create(CoachFormType::class, $coachDto);
        $form->submit($content);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setName($coachDto->name);
            $user->setSurname($coachDto->surname);
            $user = $this->userManager->save($user);

            $coach->setPerson($user);
            $this->coachManager->save($coach);
            // ToDO: it will be better send notification after a domain event
            $this->notificationManager->sendNewUserNotification($coach->getPerson());
            return [$coach, null];
        }
        return [null, $form];
    }
}
