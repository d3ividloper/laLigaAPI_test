<?php

namespace App\Service\PersonClub;

use App\Entity\PersonClub;
use App\Repository\PersonClubRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClubMemberManager {
    public const TYPE_COACH = 'type_coach';
    public const TYPE_PLAYER = 'type_player';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PersonClubRepository
     */
    private $clubMemberRepository;


    public function __construct(EntityManagerInterface $entityManager, PersonClubRepository $clubMemberRepository)
    {
        $this->entityManager = $entityManager;
        $this->clubMemberRepository = $clubMemberRepository;
    }

    public function find(int $id): ?PersonClub
    {
        return $this->clubMemberRepository->find($id);
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

    public function findBy(array $criteria): array
    {
        return $this->clubMemberRepository->findBy($criteria);
    }
}
