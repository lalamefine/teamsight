<?php

namespace App\Entity\Feedback360;

use App\Entity\WebUser;
use App\Repository\Feedback360\Observation360Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Observation360Repository::class)]
class Observation360 implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'observation360s')]
    #[ORM\JoinColumn(nullable: false)]
    private WebUser $agent;

    #[ORM\ManyToOne(inversedBy: 'observation360s')]
    private ?CampaignFeedback360 $campaign = null;

    #[ORM\Column(length: 32)]
    private string $state = self::STATE_CREATED;
    public const STATE_CREATED = 'created';
    public const STATE_PANEL_EVALUE = 'panel_evalue';
    public const STATE_PANEL_HIERARCHY = 'panel_hierarchy';
    public const STATE_READY = 'ready';
    public const STATE_OPEN = 'open';
    public const STATE_CLOSED = 'closed';
    public const STATE_VALIDATED = 'validated';
    public const STATES = [
        self::STATE_CREATED,
        self::STATE_PANEL_EVALUE,
        self::STATE_PANEL_HIERARCHY,
        self::STATE_READY,
        self::STATE_OPEN,
        self::STATE_CLOSED,
        self::STATE_VALIDATED,
    ];

    /**
     * @var Collection<int, Observer>
     */
    #[ORM\OneToMany(targetEntity: Observer::class, mappedBy: 'observation', orphanRemoval: true)]
    private Collection $observers;

    public function __construct(?CampaignFeedback360 $campaign, WebUser $agent)
    {
        $this->agent = $agent;
        $this->campaign = $campaign;
        if ($campaign !== null) {
            $campaign->addObservation360($this);
        }
        $this->observers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgent(): ?WebUser
    {
        return $this->agent;
    }

    public function setAgent(?WebUser $agent): static
    {
        $this->agent = $agent;

        return $this;
    }

    public function getCampaign(): ?CampaignFeedback360
    {
        return $this->campaign;
    }

    public function setCampaign(?CampaignFeedback360 $campaign): static
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function stateDisplayed(): string
    {
        return match ($this->state) {
            self::STATE_CREATED => 'En construction',
            self::STATE_PANEL_EVALUE => 'Panel à compléter',
            self::STATE_PANEL_HIERARCHY => 'Panel à valider',
            self::STATE_READY => 'Panel prêt',
            self::STATE_OPEN => 'Ouverte',
            self::STATE_CLOSED => 'Fermée',
            self::STATE_VALIDATED => 'Rapport Validé',
            default => 'Inconnu',
        };
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function hasObserver(WebUser $user): bool
    {
        foreach ($this->observers as $observer) {
            if ($observer->getAgent() === $user) {
                return true;
            }
        }
        return false;
    }

    public function isStateOrAfter(string $state): bool
    {
        return array_search($this->state, self::STATES) >= array_search($state, self::STATES);
    }

    public function isStateOrBefore(string $state): bool
    {
        return array_search($this->state, self::STATES) <= array_search($state, self::STATES);
    }
    public function isStateAfter(string $state): bool
    {
        return array_search($this->state, self::STATES) > array_search($state, self::STATES);
    }

    public function isStateBefore(string $state): bool
    {
        return array_search($this->state, self::STATES) < array_search($state, self::STATES);
    }

    public function isPanelInsufficient(): bool
    {
        return $this->observers->count() < $this->campaign->getCompany()->getConfig()->getPanelMinSize();
    }

    public function isPanelExcessive(): bool
    {
        $max = $this->campaign->getCompany()->getConfig()->getPanelMaxSize();
        return $max !== null && $this->observers->count() > $max;
    }

    public function isPanelSizeValid(): bool
    {
        return !$this->isPanelInsufficient() && !$this->isPanelExcessive();
    }

    /**
     * @return Collection<int, Observer>
     */
    public function getObservers(): Collection
    {
        return $this->observers;
    }

    public function addObserver(Observer $observer): static
    {
        if (!$this->observers->contains($observer)) {
            $this->observers->add($observer);
            $observer->setObservation($this);
        }
        $this->autoUpdateState();
        return $this;
    }

    public function removeObserver(Observer $observer): static
    {
        if ($this->observers->removeElement($observer)) {
            // set the owning side to null (unless already changed)
            if ($observer->getObservation() === $this) {
                $observer->setObservation(null);
            }
        }
        $this->autoUpdateState();
        return $this;
    }

    public function autoUpdateState(): void
    {
        $companyConfig = $this->campaign->getCompany()->getConfig();
        // CREATED -> PANEL_EVALUE
        if ($companyConfig->isFdb360askPanelToEvalue() && $this->state === self::STATE_CREATED) {
            if($this->campaign->getPanelProposalOpenedAt() !== null && $this->campaign->getPanelProposalOpenedAt() < new \DateTime()) {
                $this->setState(self::STATE_PANEL_EVALUE);
            }
        }
        // PANEL_EVALUE/CREATED -> PANEL_HIERARCHY
        if ($companyConfig->isFdb360askPanelToHierarchy() && $this->state === ($companyConfig->isFdb360askPanelToEvalue() ? self::STATE_PANEL_EVALUE : self::STATE_CREATED)) {
            if($this->campaign->getPanelProposalEvalueClosedAt() !== null && $this->campaign->getPanelProposalEvalueClosedAt() < new \DateTime()) {
                $this->setState(self::STATE_PANEL_HIERARCHY);
            }
        }
        // PANEL_HIERARCHY -> READY
        if ($companyConfig->isFdb360askPanelToHierarchy() && $this->state === self::STATE_PANEL_HIERARCHY) {
            if ($this->campaign->getPanelProposalHierarchyClosedAt() !== null && $this->campaign->getPanelProposalHierarchyClosedAt() < new \DateTime()) {
                $this->setState(self::STATE_READY);
            }
        }
        // CREATED -> READY
        if (!$companyConfig->isFdb360askPanelToHierarchy() && !$companyConfig->isFdb360askPanelToEvalue() && $this->isStateBefore(self::STATE_READY)) {
            $this->setState(self::STATE_READY);
        }
    }

    public function __toString(): string
    {
        return $this->agent->getFullName() . ' ~ ' . ($this->campaign ? $this->campaign->getName() : 'Sans campagne');
    }
}
