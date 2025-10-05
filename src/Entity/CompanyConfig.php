<?php

namespace App\Entity;

use App\Repository\CompanyConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyConfigRepository::class)]
class CompanyConfig
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'config', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\Column(length: 32, options: ['default' => 'email'])]
    private ?string $agtIdType = 'email';

    #[ORM\Column(length: 32, options: ['default' => 'email-pass'])]
    private ?string $agtAuthType = 'email-pass';

    #[ORM\Column(options: ['default' => true])]
    private bool $useTeamGrouping = true;

    #[ORM\Column(options: ['default' => true])]
    private bool $useAccountDynCamp = true;

    #[ORM\Column(options: ['default' => 36])]
    private int $dataRetention = 36;

    ////////////// 360 Feedback //////////////
    #[ORM\Column(options: ['default' => true])]
    private bool $questFdb360 = true;

    #[ORM\Column(options: ['default' => false])]
    private bool $fdb360askPanelToEvalue = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $fdb360askPanelToHierarchy = false;

    #[ORM\Column(options: ['default' => true])]
    private bool $useAccountDynPan = true;

    #[ORM\Column(options: ['default' => 6])]
    private int $panelMinSize = 6;

    #[ORM\Column(nullable: true)]
    private ?int $panelMaxSize = null;

    ////////////// COMPETENCES //////////////
    #[ORM\Column(options: ['default' => true])]
    private bool $questComp = true;

    #[ORM\Column(options: ['default' => true])]
    private bool $useCompRef = true;

    ////////////// EA //////////////
    #[ORM\Column(options: ['default' => true])]
    private bool $questEA = true;

    ////////////// PERCEPTION ENTREPRISE //////////////
    #[ORM\Column(options: ['default' => true])]
    private bool $questPerc = true;

    /////////////////////////////////////

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getAgtIdType(): ?string
    {
        // $list = ['email', 'company', 'app'];
        return $this->agtIdType;
    }

    public function setAgtIdType(string $agtIdType): static
    {
        $this->agtIdType = $agtIdType;

        return $this;
    }

    public function getAgtAuthType(): ?string
    {
        // $list = 
        return $this->agtAuthType;
    }

    public function setAgtAuthType(string $agtAuthType): static
    {
        $this->agtAuthType = $agtAuthType;

        return $this;
    }

    public function isUseTeamGrouping(): bool
    {
        return $this->useTeamGrouping;
    }

    public function setUseTeamGrouping(bool $useTeamGrouping): static
    {
        $this->useTeamGrouping = $useTeamGrouping;

        return $this;
    }

    public function isUseCompRef(): bool
    {
        return $this->useCompRef;
    }

    public function setUseCompRef(bool $useCompRef): static
    {
        $this->useCompRef = $useCompRef;

        return $this;
    }

    public function isQuestFdb360(): bool
    {
        return $this->questFdb360;
    }

    public function setQuestFdb360(bool $questFdb360): static
    {
        $this->questFdb360 = $questFdb360;

        return $this;
    }

    public function isQuestComp(): bool
    {
        return $this->questComp;
    }

    public function setQuestComp(bool $questComp): static
    {
        $this->questComp = $questComp;

        return $this;
    }

    public function isQuestEA(): bool
    {
        return $this->questEA;
    }

    public function setQuestEA(bool $questEA): static
    {
        $this->questEA = $questEA;

        return $this;
    }

    public function isQuestPerc(): bool
    {
        return $this->questPerc;
    }

    public function setQuestPerc(bool $questPerc): static
    {
        $this->questPerc = $questPerc;

        return $this;
    }

    public function isUseAccountDynCamp(): bool
    {
        return $this->useAccountDynCamp;
    }

    public function setUseAccountDynCamp(bool $useAccountDynCamp): static
    {
        $this->useAccountDynCamp = $useAccountDynCamp;

        return $this;
    }

    public function isUseAccountDynPan(): bool
    {
        return $this->useAccountDynPan;
    }

    public function setUseAccountDynPan(bool $useAccountDynPan): static
    {
        $this->useAccountDynPan = $useAccountDynPan;

        return $this;
    }

    public function getDataRetention(): ?int
    {
        return $this->dataRetention;
    }

    public function setDataRetention(int $dataRetention): static
    {
        $this->dataRetention = $dataRetention;

        return $this;
    }

    public function isFdb360askPanelToEvalue(): bool
    {
        return $this->fdb360askPanelToEvalue;
    }

    public function setFdb360askPanelToEvalue(bool $fdb360askPanelToEvalue): static
    {
        $this->fdb360askPanelToEvalue = $fdb360askPanelToEvalue;

        return $this;
    }

    public function isFdb360askPanelToHierarchy(): bool
    {
        return $this->fdb360askPanelToHierarchy;
    }

    public function setFdb360askPanelToHierarchy(bool $fdb360askPanelToHierarchy): static
    {
        $this->fdb360askPanelToHierarchy = $fdb360askPanelToHierarchy;

        return $this;
    }

    public function getPanelMinSize(): int
    {
        return $this->panelMinSize;
    }

    public function setPanelMinSize(int $panelMinSize): static
    {
        $this->panelMinSize = $panelMinSize;

        return $this;
    }

    public function getPanelMaxSize(): ?int
    {
        return $this->panelMaxSize;
    }

    public function setPanelMaxSize(?int $panelMaxSize): static
    {
        $this->panelMaxSize = $panelMaxSize;

        return $this;
    }
}
