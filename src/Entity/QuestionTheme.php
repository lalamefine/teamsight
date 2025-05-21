<?php

namespace App\Entity;

use App\Repository\QuestionThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionThemeRepository::class)]
class QuestionTheme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'questionThemes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Question360>
     */
    #[ORM\OneToMany(targetEntity: Question360::class, mappedBy: 'thematique')]
    private Collection $question360s;

    public function __construct()
    {
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
            $question360->setThematique($this);
        }

        return $this;
    }

    public function removeQuestion360(Question360 $question360): static
    {
        if ($this->question360s->removeElement($question360)) {
            // set the owning side to null (unless already changed)
            if ($question360->getThematique() === $this) {
                $question360->setThematique(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
