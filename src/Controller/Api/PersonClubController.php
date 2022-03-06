<?php

namespace App\Controller\Api;


use App\Model\PersonClubRepositoryCriteria;
use App\Repository\PersonClubRepository;
use App\Service\PersonClub\ClubCoachFormProcessor;
use App\Service\PersonClub\ClubMemberDelete;
use App\Service\PersonClub\ClubMemberManager;
use App\Service\PersonClub\PersonClubFormProcessor;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PersonClubController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/clubs/{id}/players")
     * @Rest\View(serializerGroups={"club","person"}, serializerEnableMaxDepthChecks=true)
     */
    public function listClubPlayers(Request $request, string $id, PersonClubRepository $personClubRepository)
    {
        try {
            // Check our query params from Request
            $playerName = $request->query->get('playerName');
            $itemsPerPage = $request->query->get('itemsPerPage');
            $page = $request->query->get('page');

            $criteria = new PersonClubRepositoryCriteria(
                $playerName,
                $itemsPerPage !== null ? intval($itemsPerPage) : 10,
                $page !== null ? intval($page) : 1
                );
            $players =  $personClubRepository->findByCriteria($criteria, $id);
            return View::create($players, Response::HTTP_OK);

        } catch (\DomainException $exception){
            return View::create($exception->getMessage(), $exception->getCode());
        } catch (\Exception $e) {
        }
    }

    /**
     * @Rest\Post(path="/clubs/{id}/players")
     * @Rest\View(serializerGroups={"club","person"}, serializerEnableMaxDepthChecks=true)
     */
    public function savePlayerAtClub(Request $request, string $id, PersonClubFormProcessor $clubFormProcessor): View
    {
        [$club, $error] = $clubFormProcessor($request, $id);
        return View::create($club ?? $error,$club ? Response::HTTP_CREATED:Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Post(path="/clubs/{id}/coach")
     * @Rest\View(serializerGroups={"club","person"}, serializerEnableMaxDepthChecks=true)
     */
    public function saveCoachAtClub(Request $request, string $id, ClubCoachFormProcessor $clubFormProcessor): View
    {
        [$club, $error] = $clubFormProcessor($request, $id);
        return View::create($club ?? $error,$club ? Response::HTTP_CREATED:Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete(path="/clubs_users/{id}")
     * @Rest\View(serializerGroups={"club"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteClubMember(string $id, ClubMemberDelete $clubDelete)
    {
        try {
            ($clubDelete)($id);
        } catch (Throwable $t) {
            return View::create('Selected user is not present at any La Liga Club', Response::HTTP_BAD_REQUEST);
        }
        return View::create(null, Response::HTTP_OK);
    }
}
