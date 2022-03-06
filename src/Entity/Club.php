<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClubRepository::class)
 */
class Club
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
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $budget;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $badge;

    /**
     * @ORM\OneToMany(targetEntity=PersonClub::class, mappedBy="club")
     */
    private $clubMembers;

    public function __construct()
    {
        $this->clubMembers = new ArrayCollection();
    }

    /**
     * @param $name
     * @param $budget
     * @param $badge
     */
    public function __create($name, $budget, $badge)
    {
        $this->name = $name;
        $this->budget = $budget;
        $this->badge = $badge;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getBudget(): ?float
    {
        return $this->budget;
    }

    /**
     * @param float $budget
     * @return $this
     */
    public function setBudget(float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBadge(): ?string
    {
        return $this->badge;
    }

    /**
     * @param string|null $badge
     * @return $this
     */
    public function setBadge(?string $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubMembers(): Collection
    {
        return $this->clubMembers;
    }

    /**
     * @param PersonClub $clubUser
     * @return $this
     */
    public function addClubMember(PersonClub $clubUser): self
    {
        if (!$this->clubMembers->contains($clubUser)) {
            $this->clubMembers[] = $clubUser;
            $clubUser->setClub($this);
        }
        return $this;
    }

    /**
     * @param PersonClub $clubUser
     * @return $this
     */
    public function deleteClubMember(PersonClub $clubUser): self
    {
        if ($this->clubMembers->removeElement($clubUser)) {
            if ($clubUser->getClub() === $this) {
                $clubUser->setClub(null);
            }
        }
        return $this;
    }
}
