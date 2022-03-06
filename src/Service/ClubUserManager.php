<?php

namespace App\Service;

use App\Entity\PersonClub;
use App\Repository\PersonClubRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClubUserManager{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PersonClubRepository
     */
    private $personClubRepository;


    public function __construct(EntityManagerInterface $entityManager, PersonClubRepository  $clubUserRepository)
    {
        $this->entityManager = $entityManager;
        $this->personClubRepository = $clubUserRepository;
    }

    public function find(int $id): ?PersonClub
    {
        return $this->personClubRepository->find($id);
    }

    public function create(): PersonClub
    {
        return new PersonClub();
    }

    public function save(PersonClub $clubUser): PersonClub
    {
        $this->entityManager->persist($clubUser);
        $this->entityManager->flush();
        return $clubUser;
    }

    public function reload(PersonClub $clubUser): PersonClub
    {
        $this->entityManager->refresh($clubUser);
        return $clubUser;
    }
}
