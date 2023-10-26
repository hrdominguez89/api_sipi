<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * 
 * @UniqueEntity(fields="email", message="El E-Mail indicado, ya se encuentra registrado.")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=Roles::class, inversedBy="user")
     */
    private $rol;

    /**
     * @ORM\OneToMany(targetEntity=Requests::class, mappedBy="professor")
     */
    private $requests;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $fullname;

    /**
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", options={"default":TRUE})
     */
    private $active;

    public function __construct()
    {
        $this->requests = new ArrayCollection();
        $this->roles = ["ROLE_USER"];
        $this->createdAt = new \DateTime();
        $this->active = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return array_unique([$this->rol->getName()]);
    }

    public function getRol(): ?Roles
    {
        return $this->rol;
    }

    public function setRol(?Roles $rol): self
    {
        $this->rol = $rol;

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->rols = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Requests>
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Requests $request): self
    {
        if (!$this->requests->contains($request)) {
            $this->requests[] = $request;
            $request->setProfessor($this);
        }

        return $this;
    }

    public function removeRequest(Requests $request): self
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getProfessor() === $this) {
                $request->setProfessor(null);
            }
        }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDataUser(): array
    {
        return [
            "id" => $this->getId(),
            "email" => $this->getEmail(),
            "fullname" => $this->getFullname(),
            "created_at" => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            "rol_id" => $this->getRol() ? $this->getRol()->getId() : null,
            "rol_name" => $this->getRol() ? $this->getRol()->getName() : null,
            "active" => $this->isActive()
        ];
    }
}
