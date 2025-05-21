<?php

namespace App\Entity;

use App\Repository\Question360Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Question360Repository::class)]
class Question360
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $libelle;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $customResponses = null;

    #[ORM\Column]
    private bool $verbatim = false;

    /**
     * @var Collection<int, ObsProfile>
     */
    #[ORM\ManyToMany(targetEntity: ObsProfile::class, inversedBy: 'question360s')]
    private Collection $profiles;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Template360 $template = null;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $parentQuestion = null;

    #[ORM\ManyToOne(inversedBy: 'question360s')]
    private ?QuestionTheme $thematique = null;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCustomResponses(): ?array
    {
        return $this->customResponses;
    }

    public function setCustomResponses(?array $customResponses): static
    {
        $this->customResponses = $customResponses;

        return $this;
    }

    public function isVerbatim(): ?bool
    {
        return $this->verbatim;
    }

    public function setVerbatim(bool $verbatim): static
    {
        $this->verbatim = $verbatim;

        return $this;
    }

    /**
     * @return Collection<int, ObsProfile>
     */
    public function getProfiles(): Collection
    {
        return $this->profiles;
    }

    public function clearProfiles(): static
    {
        $this->profiles->clear();
        return $this;
    }

    public function addProfile(ObsProfile $profile): static
    {
        if (!$this->profiles->contains($profile)) {
            $this->profiles->add($profile);
        }

        return $this;
    }

    public function removeProfile(ObsProfile $profile): static
    {
        $this->profiles->removeElement($profile);

        return $this;
    }

    public function getTemplate(): ?Template360
    {
        return $this->template;
    }

    public function setTemplate(?Template360 $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getParentQuestion(): ?self
    {
        return $this->parentQuestion;
    }

    public function setParentQuestion(?self $parentQuestion): static
    {
        $this->parentQuestion = $parentQuestion;

        return $this;
    }

    public function getThematique(): ?QuestionTheme
    {
        return $this->thematique;
    }

    public function setThematique(?QuestionTheme $thematique): static
    {
        $this->thematique = $thematique;

        return $this;
    }

}
