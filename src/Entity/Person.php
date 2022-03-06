<?php

namespace App\Entity;

use App\Repository\CoachRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CoachRepository::class)
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\OneToOne(targetEntity=PersonClub::class, mappedBy="person", cascade={"persist", "remove"})
     */
    private $personClub;

    /**
     * Class methods
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPersonClub(): ?PersonClub
    {
        return $this->personClub;
    }

    public function setPersonClub(PersonClub $clubUser): self
    {
        if ($clubUser->getPerson() !== $this) {
            $clubUser->setPerson($this);
        }

        $this->clubUser = $clubUser;

        return $this;
    }
}
