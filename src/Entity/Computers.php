<?php

namespace App\Entity;

use App\Constants\Constants;
use App\Repository\ComputersRepository;
use App\Repository\StatusComputerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ComputersRepository::class)
 */
class Computers
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
     * @ORM\Column(type="string", length=20)
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $model;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $serie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\ManyToOne(targetEntity=StatusComputer::class, inversedBy="computers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statusComputer;

    /**
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=ProgramsComputers::class, mappedBy="computer")
     */
    private $programsComputers;

    /**
     * @ORM\OneToMany(targetEntity=RequestsComputers::class, mappedBy="computer")
     */
    private $requestsComputers;

    /**
     * @ORM\Column(type="boolean", nullable=true,options={"default":TRUE})
     */
    private $visible;

    public function __construct()
    {
        $this->programsComputers = new ArrayCollection();
        $this->requestsComputers = new ArrayCollection();
        $this->createdAt =  new \DateTime();
        $this->visible =  true;
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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function setSerie(string $serie): self
    {
        $this->serie = $serie;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getStatusComputer(): ?StatusComputer
    {
        return $this->statusComputer;
    }

    public function setStatusComputer(?StatusComputer $statusComputer): self
    {
        $this->statusComputer = $statusComputer;

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
            $programsComputer->setComputer($this);
        }

        return $this;
    }

    public function removeProgramsComputer(ProgramsComputers $programsComputer): self
    {
        if ($this->programsComputers->removeElement($programsComputer)) {
            // set the owning side to null (unless already changed)
            if ($programsComputer->getComputer() === $this) {
                $programsComputer->setComputer(null);
            }
        }

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
            $requestsComputer->setComputer($this);
        }

        return $this;
    }

    public function removeRequestsComputer(RequestsComputers $requestsComputer): self
    {
        if ($this->requestsComputers->removeElement($requestsComputer)) {
            // set the owning side to null (unless already changed)
            if ($requestsComputer->getComputer() === $this) {
                $requestsComputer->setComputer(null);
            }
        }

        return $this;
    }

    public function getDataComputers($data): array
    {
        if ($data) {
            return [
                'id' => $this->getId(),
                'name' => $this->getName(),
                'brand' => $this->getBrand(),
                'model' => $this->getModel(),
                'serie' => $this->getSerie(),
                'details' => $this->getDetails(),
                'status_computer_id' => $this->getStatusComputer() ? $this->getStatusComputer()->getId() : null,
                'status_computer_name' => $this->getStatusComputer() ? $this->getStatusComputer()->getName() : null,
                'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
                'request_computer_id' => $data
            ];
        }
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'brand' => $this->getBrand(),
            'model' => $this->getModel(),
            'serie' => $this->getSerie(),
            'details' => $this->getDetails(),
            'status_computer_id' => $this->getStatusComputer() ? $this->getStatusComputer()->getId() : null,
            'status_computer_name' => $this->getStatusComputer() ? $this->getStatusComputer()->getName() : null,
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }
}
