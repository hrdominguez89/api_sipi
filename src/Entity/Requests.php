<?php

namespace App\Entity;

use App\Repository\RequestsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RequestsRepository::class)
 */
class Requests
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="requests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $professor;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $requestedPrograms;

    /**
     * @ORM\Column(type="integer")
     */
    private $requestedAmount;

    /**
     * @ORM\Column(type="date")
     */
    private $requestedDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observations;

    /**
     * @ORM\ManyToOne(targetEntity=StatusRequest::class, inversedBy="requests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statusRequest;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=RequestsComputers::class, mappedBy="request")
     */
    private $requestsComputers;

    public function __construct()
    {
        $this->requestsComputers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfessor(): ?Users
    {
        return $this->professor;
    }

    public function setProfessor(?Users $professor): self
    {
        $this->professor = $professor;

        return $this;
    }

    public function getRequestedPrograms(): ?string
    {
        return $this->requestedPrograms;
    }

    public function setRequestedPrograms(?string $requestedPrograms): self
    {
        $this->requestedPrograms = $requestedPrograms;

        return $this;
    }

    public function getRequestedAmount(): ?int
    {
        return $this->requestedAmount;
    }

    public function setRequestedAmount(int $requestedAmount): self
    {
        $this->requestedAmount = $requestedAmount;

        return $this;
    }

    public function getRequestedDate(): ?\DateTimeInterface
    {
        return $this->requestedDate;
    }

    public function setRequestedDate(\DateTimeInterface $requestedDate): self
    {
        $this->requestedDate = $requestedDate;

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

    public function getStatusRequest(): ?StatusRequest
    {
        return $this->statusRequest;
    }

    public function setStatusRequest(?StatusRequest $statusRequest): self
    {
        $this->statusRequest = $statusRequest;

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

    /**
     * @return Collection<int, RequestsComputers>
     */
    public function getRequestsComputers(): Collection
    {
        return $this->requestsComputers;
    }

    public function addRequestsComputer(RequestsComputers $requestsComputer): self
    {
        if (!$this->requestsComputers->contains($requestsComputer)) {
            $this->requestsComputers[] = $requestsComputer;
            $requestsComputer->setRequest($this);
        }

        return $this;
    }

    public function removeRequestsComputer(RequestsComputers $requestsComputer): self
    {
        if ($this->requestsComputers->removeElement($requestsComputer)) {
            // set the owning side to null (unless already changed)
            if ($requestsComputer->getRequest() === $this) {
                $requestsComputer->setRequest(null);
            }
        }

        return $this;
    }
}
