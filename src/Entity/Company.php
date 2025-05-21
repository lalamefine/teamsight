<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, options: ['default' => '[]'])]
    private ?array $enabledFeatures = ['*'];

    /**
     * @var Collection<int, WebUser>
     */
    #[ORM\OneToMany(targetEntity: WebUser::class, mappedBy: 'company')]
    private Collection $webUsers;

    #[ORM\OneToOne(mappedBy: 'company', cascade: ['persist', 'remove'])]
    private ?CompanyConfig $config = null;

    /**
     * @var Collection<int, ObsProfile>
     */
    #[ORM\OneToMany(targetEntity: ObsProfile::class, mappedBy: 'company', orphanRemoval: true)]
    private Collection $obsProfiles;

    /**
     * @var Collection<int, Template360>
     */
    #[ORM\OneToMany(targetEntity: Template360::class, mappedBy: 'company')]
    private Collection $template360s;

    /**
     * @var Collection<int, QuestionTheme>
     */
    #[ORM\OneToMany(targetEntity: QuestionTheme::class, mappedBy: 'company', orphanRemoval: true)]
    private Collection $questionThemes;

    public function __construct()
    {
        $this->webUsers = new ArrayCollection();
        $this->obsProfiles = new ArrayCollection();
        $this->template360s = new ArrayCollection();
        $this->questionThemes = new ArrayCollection();
    }

    public function initObsProfiles(): void
    {
        $this->obsProfiles = new ArrayCollection([
            new ObsProfile('Observé', false, $this, false, false, true),
            new ObsProfile('Hierarchie', false, $this, true, false, true),
        ]);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getEnabledFeatures(): array
    {
        return $this->enabledFeatures;
    }

    public function setEnabledFeatures(array $enabledFeatures): static
    {
        $this->enabledFeatures = $enabledFeatures;

        return $this;
    }

    /**
     * @return Collection<int, WebUser>
     */
    public function getWebUsers(): Collection
    {
        return $this->webUsers;
    }

    public function addWebUser(WebUser $webUser): static
    {
        if (!$this->webUsers->contains($webUser)) {
            $this->webUsers->add($webUser);
            $webUser->setCompany($this);
        }

        return $this;
    }

    public function removeWebUser(WebUser $webUser): static
    {
        if ($this->webUsers->removeElement($webUser)) {
            // set the owning side to null (unless already changed)
            if ($webUser->getCompany() === $this) {
                $webUser->setCompany(null);
            }
        }

        return $this;
    }

    public function getConfig(): ?CompanyConfig
    {
        return $this->config;
    }

    public function setConfig(CompanyConfig $config): static
    {
        // set the owning side of the relation if necessary
        if ($config->getCompany() !== $this) {
            $config->setCompany($this);
        }

        $this->config = $config;

        return $this;
    }

    /**
     * @return Collection<int, ObsProfile>
     */
    public function getObsProfiles(): Collection
    {
        return $this->obsProfiles;
    }

    public function addObsProfile(ObsProfile $obsProfile): static
    {
        if (!$this->obsProfiles->contains($obsProfile)) {
            $this->obsProfiles->add($obsProfile);
            $obsProfile->setCompany($this);
        }

        return $this;
    }

    public function removeObsProfile(ObsProfile $obsProfile): static
    {
        if ($this->obsProfiles->removeElement($obsProfile)) {
            // set the owning side to null (unless already changed)
            if ($obsProfile->getCompany() === $this) {
                $obsProfile->setCompany(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Template360>
     */
    public function getTemplate360s(): Collection
    {
        return $this->template360s;
    }

    public function addTemplate360(Template360 $template360): static
    {
        if (!$this->template360s->contains($template360)) {
            $this->template360s->add($template360);
            $template360->setCompany($this);
        }

        return $this;
    }

    public function removeTemplate360(Template360 $template360): static
    {
        if ($this->template360s->removeElement($template360)) {
            // set the owning side to null (unless already changed)
            if ($template360->getCompany() === $this) {
                $template360->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuestionTheme>
     */
    public function getQuestionThemes(): Collection
    {
        return $this->questionThemes;
    }

    public function addQuestionTheme(QuestionTheme $questionTheme): static
    {
        if (!$this->questionThemes->contains($questionTheme)) {
            $this->questionThemes->add($questionTheme);
            $questionTheme->setCompany($this);
        }

        return $this;
    }

    public function removeQuestionTheme(QuestionTheme $questionTheme): static
    {
        if ($this->questionThemes->removeElement($questionTheme)) {
            // set the owning side to null (unless already changed)
            if ($questionTheme->getCompany() === $this) {
                $questionTheme->setCompany(null);
            }
        }

        return $this;
    }
}
