<?php

namespace App\Entity;

use App\Repository\StudentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: StudentsRepository::class)]
#[UniqueEntity(fields: "dni", message: "El DNI indicado, ya se encuentra registrado.")]
class Students
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "integer", unique: true)]
    private $dni;

    #[ORM\Column(type: "string", length: 50)]
    private $fullname;

    #[ORM\OneToMany(targetEntity: RequestsComputers::class, mappedBy: "student")]
    private $requestsComputers;

    #[ORM\Column(type: "boolean", nullable: true, options: ["default" => TRUE])]
    private $visible;

    public function __construct()
    {
        $this->requestsComputers = new ArrayCollection();
        $this->visible =  true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDni(): ?int
    {
        return $this->dni;
    }

    public function setDni(int $dni): self
    {
        $this->dni = $dni;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

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
            $requestsComputer->setStudent($this);
        }

        return $this;
    }

    public function removeRequestsComputer(RequestsComputers $requestsComputer): self
    {
        if ($this->requestsComputers->removeElement($requestsComputer)) {
            // set the owning side to null (unless already changed)
            if ($requestsComputer->getStudent() === $this) {
                $requestsComputer->setStudent(null);
            }
        }

        return $this;
    }

    public function getDataStudent(): array
    {
        return [
            'id' => $this->getId(),
            'dni' => $this->getDni(),
            'nombre_completo' => $this->getFullname()
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
