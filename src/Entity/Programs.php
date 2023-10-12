<?php

namespace App\Entity;

use App\Repository\ProgramsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProgramsRepository::class)
 */
class Programs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $version;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observations;

    /**
     * @ORM\OneToMany(targetEntity=ProgramsComputers::class, mappedBy="program")
     */
    private $programsComputers;

    public function __construct()
    {
        $this->programsComputers = new ArrayCollection();
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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): self
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * @return Collection<int, ProgramsComputers>
     */
    public function getProgramsComputers(): Collection
    {
        return $this->programsComputers;
    }

    public function addProgramsComputer(ProgramsComputers $programsComputer): self
    {
        if (!$this->programsComputers->contains($programsComputer)) {
            $this->programsComputers[] = $programsComputer;
            $programsComputer->setProgram($this);
        }

        return $this;
    }

    public function removeProgramsComputer(ProgramsComputers $programsComputer): self
    {
        if ($this->programsComputers->removeElement($programsComputer)) {
            // set the owning side to null (unless already changed)
            if ($programsComputer->getProgram() === $this) {
                $programsComputer->setProgram(null);
            }
        }

        return $this;
    }
}
