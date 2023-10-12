<?php

namespace App\Entity;

use App\Repository\StatusComputerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatusComputerRepository::class)
 */
class StatusComputer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Computers::class, mappedBy="statusComputer")
     */
    private $computers;

    public function __construct()
    {
        $this->computers = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Computers>
     */
    public function getComputers(): Collection
    {
        return $this->computers;
    }

    public function addComputer(Computers $computer): self
    {
        if (!$this->computers->contains($computer)) {
            $this->computers[] = $computer;
            $computer->setStatusComputer($this);
        }

        return $this;
    }

    public function removeComputer(Computers $computer): self
    {
        if ($this->computers->removeElement($computer)) {
            // set the owning side to null (unless already changed)
            if ($computer->getStatusComputer() === $this) {
                $computer->setStatusComputer(null);
            }
        }

        return $this;
    }
}
