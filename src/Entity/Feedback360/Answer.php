<?php

namespace App\Entity\Feedback360;

use App\Repository\Feedback360\AnswerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question360 $question = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Observation360 $observation = null;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Observer $by = null;

    #[ORM\Column(nullable: true)]
    private ?float $value = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?Question360
    {
        return $this->question;
    }

    public function setQuestion(?Question360 $question): static
    {
        $this->question = $question;

        return $this;
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

    public function getBy(): ?Observer
    {
        return $this->by;
    }

    public function setBy(?Observer $by): static
    {
        $this->by = $by;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
