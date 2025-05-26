<?php

namespace App\Entity;

use App\Repository\CampaignFeedback360Repository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampaignFeedback360Repository::class)]
class CampaignFeedback360
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'campaigns')]
    #[ORM\JoinColumn(nullable: false)]
    private ?company $company = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $panelProposalOpenedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $panelProposalClosedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $beginAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endAt = null;
    
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $reportValidationDeadline = null;

    #[ORM\Column(length: 16)]
    private string $currentState = self::STATE_DRAFT;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?company
    {
        return $this->company;
    }

    public function setCompany(?company $company): static
    {
        $this->company = $company;

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

    public function getBeginAt(): ?\DateTimeImmutable
    {
        return $this->beginAt;
    }

    public function setBeginAt(?\DateTimeImmutable $beginAt): static
    {
        if ($beginAt && $this->endAt && $beginAt > $this->endAt) {
            throw new \InvalidArgumentException('Begin date cannot be after end date.');
        }
        if ($beginAt && $this->panelProposalClosedAt && $beginAt < $this->panelProposalClosedAt) {
            throw new \InvalidArgumentException('Begin date cannot be before panel proposal closed date.');
        }
        $this->beginAt = $beginAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): static
    {
        if ($endAt && $this->beginAt && $endAt < $this->beginAt) {
            throw new \InvalidArgumentException('End date cannot be before begin date.');
        }
        if ($endAt && $this->panelProposalClosedAt && $endAt < $this->panelProposalClosedAt) {
            throw new \InvalidArgumentException('End date cannot be before panel proposal closed date.');
        }
        $this->endAt = $endAt;

        return $this;
    }

    public function getType(): string
    {
        return "Feedback360";
    }

    public function getPanelProposalOpenedAt(): ?\DateTimeImmutable
    {
        return $this->panelProposalOpenedAt;
    }

    public function setPanelProposalOpenedAt(?\DateTimeImmutable $panelProposalOpenedAt): static
    {
        $this->panelProposalOpenedAt = $panelProposalOpenedAt;

        return $this;
    }

    public function getPanelProposalClosedAt(): ?\DateTimeImmutable
    {
        return $this->panelProposalClosedAt;
    }

    public function setPanelProposalClosedAt(?\DateTimeImmutable $panelProposalClosedAt): static
    {
        if(!$this->panelProposalOpenedAt) {
            throw new \InvalidArgumentException('Panel proposal opened date must be set before closing it.');
        }

        if ($this->panelProposalClosedAt < $this->panelProposalOpenedAt) {
            throw new \InvalidArgumentException('Panel proposal closed date cannot be before opened date.');
        }
        $this->panelProposalClosedAt = $panelProposalClosedAt;

        return $this;
    }

    public function getCurrentState(): ?string
    {
        return $this->currentState;
    }

    public const STATE_DRAFT = 'draft';
    public const STATE_PROP_OPEN = 'proposal_open';
    public const STATE_PROP_CLOSED = 'proposal_closed';
    public const STATE_ANS_OPEN = 'answ_opened';
    public const STATE_ANS_CLOSED = 'answ_closed';
    public function setCurrentState(string $currentState): static
    {
        if (!in_array($currentState, [
            self::STATE_DRAFT,
            self::STATE_PROP_OPEN,
            self::STATE_PROP_CLOSED,
            self::STATE_ANS_OPEN,
            self::STATE_ANS_CLOSED,
        ])) {
            throw new \InvalidArgumentException('Invalid state: ' . $currentState);
        }
        $this->currentState = $currentState;
        return $this;
    }

    public function getReportValidationDeadline(): ?\DateTimeImmutable
    {
        return $this->reportValidationDeadline;
    }

    public function setReportValidationDeadline(?\DateTimeImmutable $reportValidationDeadline): static
    {
        if ($reportValidationDeadline < $this->endAt) {
            throw new \InvalidArgumentException('La date limite de validation du rapport ne peut pas être avant la date de fin.');
        }

        $this->reportValidationDeadline = $reportValidationDeadline;

        return $this;
    }
}
