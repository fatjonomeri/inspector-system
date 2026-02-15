<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Repository\JobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\Table(name: 'jobs')]
#[ApiResource(
    operations: [
        new Get(security: "
            is_granted('ROLE_ADMIN') or (
                is_granted('ROLE_INSPECTOR') and (
                    (object.getStatus() == 'available' and object.getLocation() == user.getLocation()) 
                    or 
                    (object.getAssignedTo() == user)
                )
            )
        "),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['job:create']],
            openapi: new \ApiPlatform\OpenApi\Model\Operation(
                requestBody: new \ApiPlatform\OpenApi\Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['title', 'location'],
                                'properties' => [
                                    'title' => ['type' => 'string', 'example' => 'Safety Inspection - London Office'],
                                    'description' => ['type' => 'string', 'example' => 'Annual safety inspection of electrical systems'],
                                    'location' => ['type' => 'string', 'example' => '/api/locations/1', 'description' => 'IRI of the location']
                                ]
                            ]
                        ]
                    ])
                )
            )
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['status' => 'exact', 'location.id' => 'exact'])]
class Job
{
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_COMPLETED = 'completed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['job:create'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['job:create'])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: [self::STATUS_AVAILABLE, self::STATUS_ASSIGNED, self::STATUS_COMPLETED])]
    private string $status = self::STATUS_AVAILABLE;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $assignedTo = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $scheduledDate = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $assessment = null;

    #[Assert\NotBlank]
    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['job:create'])]
    private ?Location $location = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = self::STATUS_AVAILABLE;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): static
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    public function getScheduledDate(): ?\DateTimeImmutable
    {
        return $this->scheduledDate;
    }

    public function setScheduledDate(?\DateTimeImmutable $scheduledDate): static
    {
        $this->scheduledDate = $scheduledDate;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getAssessment(): ?string
    {
        return $this->assessment;
    }

    public function setAssessment(?string $assessment): static
    {
        $this->assessment = $assessment;
        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function assignTo(User $inspector, \DateTimeImmutable $scheduledDate): void
    {
        if ($this->status !== self::STATUS_AVAILABLE) {
            throw new \LogicException('Only available jobs can be assigned');
        }

        $this->assignedTo = $inspector;
        $this->scheduledDate = $scheduledDate;
        $this->status = self::STATUS_ASSIGNED;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function complete(string $assessment, ?\DateTimeImmutable $completedAt = null): void
    {
        if ($this->status !== self::STATUS_ASSIGNED) {
            throw new \LogicException('Only assigned jobs can be completed');
        }

        $this->assessment = $assessment;
        $this->completedAt = $completedAt ?? new \DateTimeImmutable();
        $this->status = self::STATUS_COMPLETED;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
