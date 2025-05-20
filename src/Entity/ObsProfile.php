<?php

namespace App\Entity;

use App\Repository\ObsProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObsProfileRepository::class)]
class ObsProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'obsProfiles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(options: ['default' => false])]
    private bool $anonymous = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $canValidateReport = false;

    #[ORM\Column(options: ['default' => true])]
    private bool $editable = true;

    #[ORM\Column(options: ['default' => false])]
    private bool $canSeeValidatedReport = false;

    /**
     * @var Collection<int, Question360>
     */
    #[ORM\ManyToMany(targetEntity: Question360::class, mappedBy: 'profiles')]
    private Collection $question360s;

    public function __construct(string $name = '', bool $anonymous = true, ?Company $company = null, bool $canValidateReport = false, bool $editable = true, bool $canSeeValidatedReport = false)
    {
        $this->name = $name;
        $this->anonymous = $anonymous;
        $this->canValidateReport = $canValidateReport;
        $this->editable = $editable;
        $this->canSeeValidatedReport = $canSeeValidatedReport;
        $this->company = $company;
        $this->question360s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isAnonymous(): bool
    {
        return $this->anonymous;
    }

    public function setAnonymous(bool $anonymous): static
    {
        $this->anonymous = $anonymous;

        return $this;
    }

    public function isCanValidateReport(): bool
    {
        return $this->canValidateReport;
    }

    public function setCanValidateReport(bool $canValidateReport): static
    {
        $this->canValidateReport = $canValidateReport;

        return $this;
    }

    public function isEditable(): bool
    {
        return $this->editable;
    }

    public function setEditable(bool $editable): static
    {
        $this->editable = $editable;

        return $this;
    }

    public function canSeeValidatedReport(): bool
    {
        return $this->canSeeValidatedReport;
    }

    public function setCanSeeValidatedReport(bool $canSeeValidatedReport): static
    {
        $this->canSeeValidatedReport = $canSeeValidatedReport;

        return $this;
    }

    /**
     * @return Collection<int, Question360>
     */
    public function getQuestion360s(): Collection
    {
        return $this->question360s;
    }

    public function addQuestion360(Question360 $question360): static
    {
        if (!$this->question360s->contains($question360)) {
            $this->question360s->add($question360);
            $question360->addProfile($this);
        }

        return $this;
    }

    public function removeQuestion360(Question360 $question360): static
    {
        if ($this->question360s->removeElement($question360)) {
            $question360->removeProfile($this);
        }

        return $this;
    }
}
