<?php

namespace App\Controller\Api;

use App\Repository\PlayerRepository;
use App\Service\Player\PlayerFormProcessor;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlayersController extends AbstractFOSRestController {

    /**
     * @Rest\Get(path="/players")
     * @Rest\View(serializerGroups={"player", "person"}, serializerEnableMaxDepthChecks=true)
     */
    public function list(PlayerRepository $repository): array
    {
        return $repository->findAll();
    }


    /**
     * @Rest\Post(path="/players")
     * @Rest\View(serializerGroups={"player", "person"}, serializerEnableMaxDepthChecks=true)
     */
    public function create(Request $request, PlayerFormProcessor $playerFormProcessor): View
    {
        [$player, $error] = $playerFormProcessor($request);
        return View::create($player ?? $error, $player ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }

    /**
     * TODO : ADD PATCH and DELETE
     */


}
