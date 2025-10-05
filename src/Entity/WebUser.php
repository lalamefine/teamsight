<?php

namespace App\Entity;

use App\Abstraction\CompanyUserInterface;
use App\Entity\Feedback360\Observation360;
use App\Entity\Feedback360\Observer;
use App\Entity\Feedback360\Observers;
use App\Repository\WebUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: WebUserRepository::class)]
#[ORM\UniqueConstraint(name: 'email_unique', columns: ['email', 'company_id'])]
#[ORM\Index(name: 'compid_unique', columns: ['company_internal_id', 'company_id'])]
class WebUser implements CompanyUserInterface, PasswordAuthenticatedUserInterface, Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hpass = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private ?array $roles = [];

    #[ORM\Column(options: ['default' => true])]
    private bool $canConnect = true;

    #[ORM\Column(options: ['default' => true])]
    private bool $displayed = true;

    #[ORM\Column(options: ['default' => false])]
    private bool $emailValidated = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyInternalID = null;

    #[ORM\ManyToOne(inversedBy: 'webUsers')]
    private ?Company $company = null;

    #[ORM\Column(length: 64)]
    private string $firstName;

    #[ORM\Column(length: 64)]
    private string $lastName;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $job = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $team = null;

    /**
     * @var Collection<int, Observation360>
     */
    #[ORM\OneToMany(targetEntity: Observation360::class, mappedBy: 'agent')]
    private Collection $observation360s;

    /**
     * @var Collection<int, Observer>
     */
    #[ORM\OneToMany(targetEntity: Observer::class, mappedBy: 'agent')]
    private Collection $observeIn; 

    public function __construct()
    {
        $this->observation360s = new ArrayCollection();
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->getUsername();
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string the hashed password for this user
     */
    public function getPassword(): string
    {
        return $this->hpass;
    }

    public function getHpass(): ?string
    {
        return $this->hpass;
    }

    public function setHpass(string $hpass): static
    {
        $this->hpass = $hpass;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function isCanConnect(): ?bool
    {
        return $this->canConnect;
    }

    public function setCanConnect(bool $canConnect): static
    {
        $this->canConnect = $canConnect;

        return $this;
    }

    public function isDisplayed(): ?bool
    {
        return $this->displayed;
    }

    public function setDisplayed(bool $displayed): static
    {
        $this->displayed = $displayed;

        return $this;
    }

    public function isEmailValidated(): ?bool
    {
        return $this->emailValidated;
    }

    public function setEmailValidated(bool $emailValidated): static
    {
        $this->emailValidated = $emailValidated;

        return $this;
    }

    public function getCompanyInternalID(): ?string
    {
        return $this->companyInternalID;
    }

    public function setCompanyInternalID(?string $companyInternalID): static
    {
        $this->companyInternalID = $companyInternalID;

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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getName(): string
    {
        return $this->getFullName();
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): static
    {
        $this->job = $job;
        return $this;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(?string $team): static
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @return Collection<int, Observation360>
     */
    public function getObservation360s(): Collection
    {
        return $this->observation360s;
    }

    public function addObservation360(Observation360 $observation360): static
    {
        if (!$this->observation360s->contains($observation360)) {
            $this->observation360s->add($observation360);
            $observation360->setAgent($this);
        }

        return $this;
    }

    public function removeObservation360(Observation360 $observation360): static
    {
        if ($this->observation360s->removeElement($observation360)) {
            // set the owning side to null (unless already changed)
            if ($observation360->getAgent() === $this) {
                $observation360->setAgent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Observation360>
     */
    public function getObserveIn(): Collection
    {
        return $this->observeIn;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
