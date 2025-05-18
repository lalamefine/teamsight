<?php

namespace App\Entity;

use App\Repository\WebUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: WebUserRepository::class)]
class WebUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $hpass = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $roles = [];

    #[ORM\Column]
    private ?bool $canConnect = null;

    #[ORM\Column]
    private ?bool $displayed = null;

    #[ORM\Column]
    private ?bool $emailValidated = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyInternalID = null;

    #[ORM\ManyToOne(inversedBy: 'webUsers')]
    private ?Company $company = null;

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
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
}
