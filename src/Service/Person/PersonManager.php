<?php

namespace App\Service\Person;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;

class PersonManager
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PersonRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, PersonRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function find(int $id): ?Person
    {
        return $this->userRepository->find($id);
    }

    public function create(): Person
    {
        return new Person();
    }

    public function save(Person $user): Person
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function reload(Person $user): Person
    {
        $this->entityManager->refresh($user);
        return $user;
    }
}
