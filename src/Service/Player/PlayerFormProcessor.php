<?php

namespace App\Service\Player;

use App\Form\Type\PlayerFormType;
use App\Service\Common\NotificationManager;
use App\Service\Person\PersonManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PlayerFormProcessor
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
     * @var PlayerManager
     */
    private $playerManager;
    /**
     * @var PlayerDtoManager
     */
    private $playerDtoManager;
    /**
     * @var NotificationManager
     */
    private $notificationManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        PersonManager $userManager,
        PlayerManager $playerManager,
        PlayerDtoManager $playerDtoManager,
        NotificationManager $notificationManager)
    {
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->playerManager = $playerManager;
        $this->playerDtoManager = $playerDtoManager;
        $this->notificationManager = $notificationManager;
    }

    public function __invoke(Request $request, ?string $userId = null): array
    {
        $playerDto = $this->playerDtoManager->create();
        if ($userId != null) {
            $player = $this->playerManager->find($userId);
            $user = $player->getPerson();

        } else {
            $user = $this->userManager->create();
            $player = $this->playerManager->create();
        }
        $content = json_decode($request->getContent(), true);
        $form = $this->formFactory->create(PlayerFormType::class, $playerDto);
        $form->submit($content);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setName($playerDto->name);
            $user->setSurname($playerDto->surname);
            $user = $this->userManager->save($user);
            $player->setPerson($user);
            $player->setPosition($playerDto->position);
            $player = $this->playerManager->save($player);
            // ToDO: it would be better send notification after a domain event
            $this->notificationManager->sendNewUserNotification($player->getPerson());
            return [$player, null];
        }
        return [null, $form];
    }
}
