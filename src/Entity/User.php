<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email = '';

    /**
     * @var string[]
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private string $password = '';

    #[ORM\Column(length: 120)]
    private string $fullName = '';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, CarbonRecord> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CarbonRecord::class, orphanRemoval: true)]
    private Collection $carbonRecords;

    /** @var Collection<int, UserEcoAction> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserEcoAction::class, orphanRemoval: true)]
    private Collection $userEcoActions;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->carbonRecords = new ArrayCollection();
        $this->userEcoActions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /** @return Collection<int, CarbonRecord> */
    public function getCarbonRecords(): Collection
    {
        return $this->carbonRecords;
    }

    public function addCarbonRecord(CarbonRecord $carbonRecord): self
    {
        if (!$this->carbonRecords->contains($carbonRecord)) {
            $this->carbonRecords->add($carbonRecord);
            $carbonRecord->setUser($this);
        }

        return $this;
    }

    public function removeCarbonRecord(CarbonRecord $carbonRecord): self
    {
        if ($this->carbonRecords->removeElement($carbonRecord)) {
            if ($carbonRecord->getUser() === $this) {
                $carbonRecord->setUser(null);
            }
        }

        return $this;
    }

    /** @return Collection<int, UserEcoAction> */
    public function getUserEcoActions(): Collection
    {
        return $this->userEcoActions;
    }

    public function addUserEcoAction(UserEcoAction $userEcoAction): self
    {
        if (!$this->userEcoActions->contains($userEcoAction)) {
            $this->userEcoActions->add($userEcoAction);
            $userEcoAction->setUser($this);
        }

        return $this;
    }

    public function removeUserEcoAction(UserEcoAction $userEcoAction): self
    {
        if ($this->userEcoActions->removeElement($userEcoAction)) {
            if ($userEcoAction->getUser() === $this) {
                $userEcoAction->setUser(null);
            }
        }

        return $this;
    }
}
