<?php

namespace App\Controller\Api;

use App\Repository\ClubRepository;
use App\Service\Club\ClubDelete;
use App\Service\Club\ClubFormProcessor;
use App\Service\Club\ClubPatchProcessor;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ClubsController extends AbstractFOSRestController {
    /**
     * @Rest\Get(path="/clubs")
     * @Rest\View(serializerGroups={"club", "person"}, serializerEnableMaxDepthChecks=true)
     */
    public function list(ClubRepository $repository): array
    {
        return $repository->findAll();
    }

    /**
     * @Rest\Post(path="/clubs")
     * @Rest\View(serializerGroups={"club","person"}, serializerEnableMaxDepthChecks=true)
     */
    public function create(Request $request, ClubFormProcessor $clubFormProcessor): View
    {
        [$club, $error] = $clubFormProcessor($request);
        return View::create($club ?? $error,$club ? Response::HTTP_CREATED:Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Patch(path="/clubs/{id}")
     * @Rest\View(serializerGroups={"club","person"}, serializerEnableMaxDepthChecks=true)
     */
    public function updatePatch(Request $request, string $id, ClubPatchProcessor $clubPatchProcessor): View
    {
        [$club, $error] = $clubPatchProcessor($request, $id);
        return View::create($club ?? $error,$club ? Response::HTTP_OK:Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put(path="/clubs/{id}")
     * @Rest\View(serializerGroups={"club","person"}, serializerEnableMaxDepthChecks=true)
     */
    public function updatePut(Request $request, string $id, ClubFormProcessor $clubFormProcessor): View
    {
        [$club, $error] = $clubFormProcessor($request, $id);
        return View::create($club ?? $error,$club ? Response::HTTP_CREATED:Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete(path="/clubs/{id}")
     * @Rest\View(serializerGroups={"club"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteAction(string $id, ClubDelete $clubDelete): View
    {
        try {
            $clubDelete($id);
        } catch (Throwable $t) {
            return View::create('El Club no puede eliminarse', Response::HTTP_BAD_REQUEST);
        }
        return View::create(null, Response::HTTP_OK);
    }
}
