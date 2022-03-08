<?php

namespace App\Service\Club;

use App\Form\Type\ClubFormType;
use App\Service\Common\FileUploader;
use App\Service\Common\NotificationManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ClubFormProcessor {
    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var ClubManager
     */
    private $clubManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ClubDtoManager
     */
    private $clubDtoManager;


    public function __construct(
        FileUploader    $fileUploader,
        FormFactoryInterface $formFactory,
        ClubDtoManager $clubDtoManager,
        ClubManager $clubManager
    ) {
        $this->fileUploader = $fileUploader;
        $this->clubManager = $clubManager;
        $this->formFactory = $formFactory;
        $this->clubDtoManager = $clubDtoManager;
    }

    public function __invoke(Request $request, ?string $clubId = null): array
    {
        $clubDto = $this->clubDtoManager->create();
        if ($clubId !== null) {
            $club = $this->clubManager->find($clubId);
        } else
            $club = $this->clubManager->create();

        $content = json_decode($request->getContent(), true);
        $form = $this->formFactory->create(ClubFormType::class, $clubDto);
        $form->submit($content);
        if ($form->isSubmitted() && $form->isValid()) {
            if(!empty($clubDto->base64Badge)) {
                $path = $this->fileUploader->uploadBase64File($clubDto->base64Badge);
                $club->setBadge($path);
            }
            $club->setName($clubDto->name);
            $club->setBudget($clubDto->budget);
            $club = $this->clubManager->save($club);
            return [$club, null];
        }
        return [null, $form];
    }
}
