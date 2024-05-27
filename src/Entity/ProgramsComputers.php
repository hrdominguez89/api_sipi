<?php

namespace App\Entity;

use App\Repository\ProgramsComputersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramsComputersRepository::class)]
class ProgramsComputers
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;


    #[ORM\ManyToOne(targetEntity: Programs::class, inversedBy: "programsComputers")]
    #[ORM\JoinColumn(nullable: false)]

    private $program;


    #[ORM\ManyToOne(targetEntity: Computers::class, inversedBy: "programsComputers")]
    #[ORM\JoinColumn(nullable: false)]

    private $computer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgram(): ?Programs
    {
        return $this->program;
    }

    public function setProgram(?Programs $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getComputer(): ?Computers
    {
        return $this->computer;
    }

    public function setComputer(?Computers $computer): self
    {
        $this->computer = $computer;

        return $this;
    }
}
