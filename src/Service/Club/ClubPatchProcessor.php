<?php

namespace App\Service\Club;

use App\Service\Common\FileUploader;
use App\Service\Common\SalaryCalculator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ClubPatchProcessor
{
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
     * @var SalaryCalculator
     */
    private $salaryCalculator;

    public function __construct(
        FileUploader $fileUploader,
        FormFactoryInterface $formFactory,
        SalaryCalculator $salaryCalculator,
        ClubManager $clubManager)
    {
        $this->fileUploader = $fileUploader;
        $this->clubManager = $clubManager;
        $this->formFactory = $formFactory;
        $this->salaryCalculator = $salaryCalculator;
    }

    public function checkNewBudget($newBudget, $clubId) {
        return $newBudget >= $this->salaryCalculator->getClubTotalSalary($clubId);
    }

    public function __invoke(Request $request, string $clubId = null): array
    {
        $club = $this->clubManager->find($clubId);
        $content = json_decode($request->getContent(), true);
        // update data according to form data
        if (array_key_exists("name", $content))
            $club->setName($content["name"]);
        if (array_key_exists("budget", $content)) {
            if ($this->checkNewBudget($content["budget"], $clubId))
                $club->setBudget($content["budget"]);
            else
                return [null, 'New budget is not enough for this Club'];
        }

        if (array_key_exists("base64Badge", $content)) {
            if (!empty($content['base64Badge'])) {
                $path = $this->fileUploader->uploadBase64File($content["base64Badge"]);
                $club->setBadge($path);
            }
        }
        $this->clubManager->save($club);
        return [$club, null];
    }
}
