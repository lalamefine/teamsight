<?php

namespace App\Entity\Feedback360;

use App\Entity\Company;
use App\Repository\Feedback360\Template360Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\UniqueConstraint(name: '', columns: ['name', 'version', 'company_id'])]
#[ORM\Entity(repositoryClass: Template360Repository::class)]
class Template360
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $version = 1;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'template360s')]
    private ?Company $company = null;

    #[ORM\Column]
    private ?int $minAnonResponse = 5;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::JSON)]
    private array $responses = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deactivatedAt = null;

    /**
     * @var Collection<int, Question360>
     */
    #[ORM\OneToMany(targetEntity: Question360::class, mappedBy: 'template', orphanRemoval: true)]
    private Collection $questions;

    #[ORM\Column(options: ['default' => false])]
    private bool $useQuestionTheme = false;

    /**
     * @var Collection<int, CampaignFeedback360>
     */
    #[ORM\OneToMany(targetEntity: CampaignFeedback360::class, mappedBy: 'baseTemplate')]
    private Collection $campaignFeedback360s;

    // ======= generated ========
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->questions = new ArrayCollection();
        $this->campaignFeedback360s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getMinAnonResponse(): ?int
    {
        return $this->minAnonResponse;
    }

    public function setMinAnonResponse(int $minAnonResponse): static
    {
        $this->minAnonResponse = $minAnonResponse;

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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getResponses(): array
    {
        return $this->responses;
    }

    public function setResponses(array $responses): static
    {
        $this->responses = $responses;

        return $this;
    }

    public function getDeactivatedAt(): ?\DateTimeImmutable
    {
        return $this->deactivatedAt;
    }

    public function setDeactivatedAt(?\DateTimeImmutable $deactivatedAt): static
    {
        $this->deactivatedAt = $deactivatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Question360>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question360 $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setTemplate($this);
        }

        return $this;
    }

    public function removeQuestion(Question360 $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getTemplate() === $this) {
                $question->setTemplate(null);
            }
        }

        return $this;
    }

    public function isUseQuestionTheme(): ?bool
    {
        return $this->useQuestionTheme;
    }

    public function setUseQuestionTheme(bool $useQuestionTheme): static
    {
        $this->useQuestionTheme = $useQuestionTheme;

        return $this;
    }

    /**
     * @return Collection<int, CampaignFeedback360>
     */
    public function getCampaignFeedback360s(): Collection
    {
        return $this->campaignFeedback360s;
    }

    public function addCampaignFeedback360(CampaignFeedback360 $campaignFeedback360): static
    {
        if (!$this->campaignFeedback360s->contains($campaignFeedback360)) {
            $this->campaignFeedback360s->add($campaignFeedback360);
            $campaignFeedback360->setBaseTemplate($this);
        }

        return $this;
    }

    public function removeCampaignFeedback360(CampaignFeedback360 $campaignFeedback360): static
    {
        if ($this->campaignFeedback360s->removeElement($campaignFeedback360)) {
            // set the owning side to null (unless already changed)
            if ($campaignFeedback360->getBaseTemplate() === $this) {
                $campaignFeedback360->setBaseTemplate(null);
            }
        }

        return $this;
    }
}
