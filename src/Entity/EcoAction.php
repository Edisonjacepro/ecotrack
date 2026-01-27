<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EcoActionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['action:read']],
    denormalizationContext: ['groups' => ['action:write']],
    security: "is_granted('ROLE_USER')"
)]
#[ORM\Entity(repositoryClass: EcoActionRepository::class)]
class EcoAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['action:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    #[Groups(['action:read', 'action:write'])]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    #[Groups(['action:read', 'action:write'])]
    private string $description = '';

    #[ORM\Column(length: 60)]
    #[Groups(['action:read', 'action:write'])]
    private string $category = '';

    #[ORM\Column]
    #[Groups(['action:read', 'action:write'])]
    private float $estimatedSavingKg = 0.0;

    #[ORM\Column]
    #[Groups(['action:read', 'action:write'])]
    private bool $active = true;

    #[ORM\Column]
    #[Groups(['action:read'])]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, UserEcoAction> */
    #[ORM\OneToMany(mappedBy: 'ecoAction', targetEntity: UserEcoAction::class, orphanRemoval: true)]
    private Collection $userEcoActions;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->userEcoActions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getEstimatedSavingKg(): float
    {
        return $this->estimatedSavingKg;
    }

    public function setEstimatedSavingKg(float $estimatedSavingKg): self
    {
        $this->estimatedSavingKg = $estimatedSavingKg;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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

    /** @return Collection<int, UserEcoAction> */
    public function getUserEcoActions(): Collection
    {
        return $this->userEcoActions;
    }

    public function addUserEcoAction(UserEcoAction $userEcoAction): self
    {
        if (!$this->userEcoActions->contains($userEcoAction)) {
            $this->userEcoActions->add($userEcoAction);
            $userEcoAction->setEcoAction($this);
        }

        return $this;
    }

    public function removeUserEcoAction(UserEcoAction $userEcoAction): self
    {
        if ($this->userEcoActions->removeElement($userEcoAction)) {
            if ($userEcoAction->getEcoAction() === $this) {
                $userEcoAction->setEcoAction(null);
            }
        }

        return $this;
    }
}
