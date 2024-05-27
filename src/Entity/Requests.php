<?php

namespace App\Entity;

use App\Repository\RequestsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestsRepository::class)]
class Requests
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;


    #[ORM\Column(type: "text", nullable: true)]

    private $requestedPrograms;


    #[ORM\Column(type: "integer")]

    private $requestedAmount;


    #[ORM\Column(type: "date")]

    private $requestedDate;


    #[ORM\Column(type: "text", nullable: true)]

    private $observations;


    #[ORM\ManyToOne(targetEntity: StatusRequest::class, inversedBy: "requests")]
    #[ORM\JoinColumn(nullable: false)]

    private $statusRequest;


    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $createdAt;


    #[ORM\OneToMany(targetEntity: RequestsComputers::class, mappedBy: "request")]
    private $requestsComputers;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "requests")]

    private $professor;

    public function __construct()
    {
        $this->requestsComputers = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProfessor(): ?User
    {
        return $this->professor;
    }

    public function setProfessor(?User $professor): self
    {
        $this->professor = $professor;

        return $this;
    }

    public function getRequestData()
    {
        return [
            'id' => $this->getId(),
            'programas_solicitados' => $this->getRequestedPrograms(),
            'equipos_solicitados' => $this->getRequestedAmount(),
            'solicitado_el' => $this->getCreatedAt()->format('Y-m-d'),
            'solicitado_para_el' => $this->getRequestedDate()->format('Y-m-d'),
            'estado' => $this->getStatusRequest()->getName()
        ];
    }

    public function getRequestDataAdmin()
    {
        return [
            'id' => $this->getId(),
            'programas_solicitados' => $this->getRequestedPrograms(),
            'equipos_solicitados' => $this->getRequestedAmount(),
            'solicitado_el' => $this->getCreatedAt()->format('Y-m-d'),
            'solicitado_para_el' => $this->getRequestedDate()->format('Y-m-d'),
            'estado' => $this->getStatusRequest()->getName(),
            'observaciones' => $this->getObservations(),
            'profesor' => $this->getProfessor()->getFullname()
        ];
    }

    public function getCalendarData()
    {
        return [
            'id' => $this->getId(),
            'equipos_solicitados' => $this->getRequestedAmount(),
            'fecha_evento' => $this->getRequestedDate()->format('Y-m-d'),
            'profesor' => $this->getProfessor()->getFullname(),
            'programas' => $this->getRequestedPrograms(),
            'observaciones' => $this->getObservations()
        ];
    }
}
