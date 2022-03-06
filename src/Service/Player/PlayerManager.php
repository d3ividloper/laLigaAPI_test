<?php

namespace App\Service\Player;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;

class PlayerManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PlayerRepository
     */
    private $playerRepository;

    public function __construct(EntityManagerInterface $entityManager, PlayerRepository $playerRepository)
    {
        $this->entityManager = $entityManager;
        $this->playerRepository = $playerRepository;
    }

    public function find(int $id): ?Player
    {
        return $this->playerRepository->findOneBy(array('user' => $id));
    }

    public function create(): Player
    {
        return new Player();
    }

    public function save(Player $player): Player
    {
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        return $player;
    }

    public function reload(Player $player): Player
    {
        $this->entityManager->refresh($player);
        return $player;
    }
}
