<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CarbonRecordRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['carbon:read']],
    denormalizationContext: ['groups' => ['carbon:write']],
    security: "is_granted('ROLE_USER')"
)]
#[ORM\Entity(repositoryClass: CarbonRecordRepository::class)]
class CarbonRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['carbon:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'carbonRecords')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['carbon:read'])]
    private ?User $user = null;

    #[ORM\Column(length: 60)]
    #[Groups(['carbon:read', 'carbon:write'])]
    private string $category = '';

    #[ORM\Column]
    #[Groups(['carbon:read', 'carbon:write'])]
    private float $amountKg = 0.0;

    #[ORM\Column]
    #[Groups(['carbon:read', 'carbon:write'])]
    private \DateTimeImmutable $recordedAt;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['carbon:read', 'carbon:write'])]
    private ?array $sourceData = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['carbon:read', 'carbon:write'])]
    private ?string $notes = null;

    public function __construct()
    {
        $this->recordedAt = new \DateTimeImmutable();
    }

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

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAmountKg(): float
    {
        return $this->amountKg;
    }

    public function setAmountKg(float $amountKg): self
    {
        $this->amountKg = $amountKg;

        return $this;
    }

    public function getRecordedAt(): \DateTimeImmutable
    {
        return $this->recordedAt;
    }

    public function setRecordedAt(\DateTimeImmutable $recordedAt): self
    {
        $this->recordedAt = $recordedAt;

        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getSourceData(): ?array
    {
        return $this->sourceData;
    }

    /**
     * @param array<string, mixed>|null $sourceData
     */
    public function setSourceData(?array $sourceData): self
    {
        $this->sourceData = $sourceData;

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
