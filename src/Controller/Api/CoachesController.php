<?php

namespace App\Controller\Api;

use App\Repository\CoachRepository;
use App\Service\Coach\CoachFormProcessor;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CoachesController extends AbstractFOSRestController {
    /**
     * @Rest\Get(path="/coaches")
     * @Rest\View(serializerGroups={"coach","person"}, serializerEnableMaxDepthChecks=true)
     */
    public function list(CoachRepository $repository): array
    {
        return $repository->findAll();
    }


    /**
     * @Rest\Post(path="/coaches")
     * @Rest\View(serializerGroups={"coach", "person"}, serializerEnableMaxDepthChecks=true)
     */
    public function create(Request $request, CoachFormProcessor $coachFormProcessor): View
    {
        [$player, $error] = $coachFormProcessor($request);
        return View::create($player ?? $error, $player ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }
}
