<?php

namespace App\Entity\Feedback360;

use App\Entity\WebUser;
use App\Repository\Feedback360\ObserverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObserverRepository::class)]
class Observer implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'observers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Observation360 $observation = null;

    #[ORM\ManyToOne(inversedBy: 'observeIn')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WebUser $agent = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $finishedAt = null;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'by', orphanRemoval: true)]
    private Collection $answers;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ObsProfile $profile = null;

    #[ORM\Column(length: 32, options: ['default' => 'locked'])]
    private string $state = self::STATE_LOCKED;
    public const STATE_WAITING = 'waiting';
    public const STATE_IN_PROGRESS = 'in_progress';
    public const STATE_COMPLETED = 'completed';
    public const STATE_LOCKED = 'locked';
    public const STATES = [
        self::STATE_LOCKED,
        self::STATE_WAITING,
        self::STATE_IN_PROGRESS,
        self::STATE_COMPLETED,
    ];

    public function __construct(Observation360 $observation, WebUser $agent, ObsProfile $profile)
    {
        $this->observation = $observation;
        $this->agent = $agent;
        $this->profile = $profile;
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObservation(): ?Observation360
    {
        return $this->observation;
    }

    public function setObservation(?Observation360 $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function getName(): string
    {
        return $this->agent->getFullName();
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

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeImmutable $finishedAt): static
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswer(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setBy($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getBy() === $this) {
                $answer->setBy(null);
            }
        }

        return $this;
    }

    public function getProfile(): ?ObsProfile
    {
        return $this->profile;
    }

    public function setProfile(?ObsProfile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    public function __toString(): string
    {
        return $this->agent->getFullName() . ' 🡆 ' . $this->observation->__toString();
    }

    public function getState(): ?string
    {
        return $this->state;
    }
    
    public function unlock(): static
    {
        return $this->setState(self::STATE_WAITING);
    }
    public function lock(): static
    {
        return $this->setState(self::STATE_LOCKED);
    }
    public function canRespond(): bool
    {
        return in_array($this->state, [self::STATE_WAITING, self::STATE_IN_PROGRESS], true);
    }
    public function isCompleted(): bool
    {
        return $this->state === self::STATE_COMPLETED;
    }
    public function openForResponse(): static
    {
        return $this->setState(self::STATE_WAITING);
    }
    public function markStarted(): static
    {
        if ($this->state === self::STATE_WAITING || $this->state === self::STATE_LOCKED) {
            $this->setStartedAt(new \DateTimeImmutable());
            return $this->setState(self::STATE_IN_PROGRESS);
        }else{
            throw new \LogicException("Cannot start an observer that is not in waiting or locked state.");
        }
        return $this;
    }
    
    public function markCompleted(): static
    {
        if ($this->state === self::STATE_IN_PROGRESS) {
            $nbAnswer = $this->answers->count();
            $nbQuestion = $this->observation->getCampaign()->getBaseTemplate()->getQuestions()->count();
            if ($nbAnswer < $nbQuestion) {
                throw new \LogicException("Cannot complete an observer that has not answered all questions. ($nbAnswer / $nbQuestion)");
            }
            $this->setFinishedAt(new \DateTimeImmutable());
            return $this->setState(self::STATE_COMPLETED);
        }else{
            throw new \LogicException("Cannot complete an observer that is not in progress.");
        }
        return $this;
    }

    public function setState(string $state): static
    {
        if (!in_array($state, self::STATES, true)) {
            throw new \InvalidArgumentException("Invalid state: $state");
        }
        $this->state = $state;

        return $this;
    }

}
