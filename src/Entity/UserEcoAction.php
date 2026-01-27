<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserEcoActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['user_action:read']],
    denormalizationContext: ['groups' => ['user_action:write']],
    security: "is_granted('ROLE_USER')"
)]
#[ORM\Entity(repositoryClass: UserEcoActionRepository::class)]
class UserEcoAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_action:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userEcoActions')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user_action:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userEcoActions')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user_action:read', 'user_action:write'])]
    private ?EcoAction $ecoAction = null;

    #[ORM\Column(length: 40)]
    #[Groups(['user_action:read', 'user_action:write'])]
    private string $status = 'planned';

    #[ORM\Column(nullable: true)]
    #[Groups(['user_action:read', 'user_action:write'])]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user_action:read', 'user_action:write'])]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['user_action:read', 'user_action:write'])]
    private ?string $notes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEcoAction(): ?EcoAction
    {
        return $this->ecoAction;
    }

    public function setEcoAction(?EcoAction $ecoAction): self
    {
        $this->ecoAction = $ecoAction;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }
}
