<?php

namespace App\Entity\Feedback360;

use App\Entity\WebUser;
use App\Repository\Feedback360\Observation360Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Observation360Repository::class)]
class Observation360
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'observation360s')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WebUser $agent = null;

    #[ORM\ManyToOne(inversedBy: 'observation360s')]
    private ?CampaignFeedback360 $campaign = null;

    #[ORM\Column(length: 32)]
    private string $state = self::STATE_CREATED;
    public const STATE_CREATED = 'created';
    public const STATE_READY = 'ready';
    public const STATE_OPEN = 'open';
    public const STATE_CLOSED = 'closed';
    public const STATE_VALIDATED = 'validated';
    public const STATES = [
        self::STATE_CREATED,
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

    public function __construct()
    {
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

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, Observers>
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

        return $this;
    }
}
