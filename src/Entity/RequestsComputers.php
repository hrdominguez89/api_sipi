<?php

namespace App\Entity;

use App\Repository\RequestsComputersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RequestsComputersRepository::class)
 */
class RequestsComputers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Requests::class, inversedBy="requestsComputers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $request;

    /**
     * @ORM\ManyToOne(targetEntity=Computers::class, inversedBy="requestsComputers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $computer;

    /**
     * @ORM\ManyToOne(targetEntity=Students::class, inversedBy="requestsComputers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $student;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $returnetAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observations;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequest(): ?Requests
    {
        return $this->request;
    }

    public function setRequest(?Requests $request): self
    {
        $this->request = $request;

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

    public function getStudent(): ?Students
    {
        return $this->student;
    }

    public function setStudent(?Students $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getReturnetAt(): ?\DateTimeInterface
    {
        return $this->returnetAt;
    }

    public function setReturnetAt(?\DateTimeInterface $returnetAt): self
    {
        $this->returnetAt = $returnetAt;

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
}
