<?php

namespace App\Service\Coach;

use App\Entity\Coach;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;

class CoachManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var CoachRepository
     */
    private $coachRepository;

    public function __construct(EntityManagerInterface $entityManager, CoachRepository $coachRepository)
    {
        $this->entityManager = $entityManager;
        $this->coachRepository = $coachRepository;
    }

    public function find(int $id): ?Coach
    {
        return $this->coachRepository->findOneBy(array('user' => $id));
    }

    public function create(): Coach
    {
        return new Coach();
    }

    public function save(Coach $coach): Coach
    {
        $this->entityManager->persist($coach);
        $this->entityManager->flush();
        return $coach;
    }

    public function reload(Coach $coach): Coach
    {
        $this->entityManager->refresh($coach);
        return $coach;
    }
}
