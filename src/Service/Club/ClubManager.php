<?php

namespace App\Service\Club;

use App\Entity\Club;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClubManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ClubRepository
     */
    private $clubRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ClubRepository $clubRepository)
    {
        $this->entityManager = $entityManager;
        $this->clubRepository = $clubRepository;
    }

    public function find(int $id): ?Club
    {
        return $this->clubRepository->find($id);
    }

    public function create(): Club
    {
        return new Club();
    }

    public function save(Club $club): Club
    {
        $this->entityManager->persist($club);
        $this->entityManager->flush();
        return $club;
    }

    public function reload(Club $club): Club
    {
        $this->entityManager->refresh($club);
        return $club;
    }
}
