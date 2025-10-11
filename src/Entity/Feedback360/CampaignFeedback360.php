<?php

namespace App\Entity\Feedback360;

use App\Abstraction\CampaignInterface;
use App\Entity\Company;
use App\Repository\Feedback360\CampaignFeedback360Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampaignFeedback360Repository::class)]
class CampaignFeedback360 implements CampaignInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'campaignFeedback360s')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $panelProposalOpenedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $panelProposalEvalueClosedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $beginAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endAt = null;
    
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $reportValidationDeadline = null;

    #[ORM\Column(length: 32)]
    private string $currentState = self::STATE_CONF;
    public const STATE_CONF = 'conf';
    public const STATE_DRAFT = 'draft';
    public const STATE_PROP_OPEN = 'proposal_open';
    public const STATE_PROP_EV_CLOSED = 'proposal_evalue_closed';
    public const STATE_PROP_HI_CLOSED = 'proposal_hierarchy_closed';
    public const STATE_READY = 'ready';
    public const STATE_ANS_OPEN = 'answ_opened';
    public const STATE_ANS_CLOSED = 'answ_closed';
    private const STATES = [
        self::STATE_CONF,
        self::STATE_DRAFT,
        self::STATE_PROP_OPEN,
        self::STATE_PROP_EV_CLOSED,
        self::STATE_PROP_HI_CLOSED,
        self::STATE_READY,
        self::STATE_ANS_OPEN,
        self::STATE_ANS_CLOSED,
    ];

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, options: ['default' => ''])]
    private array $features = [];
    private const FEATURE_PANEL_EVALUE_PROPOSAL = 'panel_evalueProposal';
    private const FEATURE_PANEL_HIERARCHY_PROPCONF = 'panel_hierarchyConfirmation';
    private const FEATURES = [
        self::FEATURE_PANEL_EVALUE_PROPOSAL,
        self::FEATURE_PANEL_HIERARCHY_PROPCONF,
    ];

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $panelProposalHierarchyClosedAt = null;

    /**
     * @var Collection<int, Observation360>
     */
    #[ORM\OneToMany(targetEntity: Observation360::class, mappedBy: 'campaign')]
    private Collection $observation360s;

    #[ORM\ManyToOne(inversedBy: 'campaignFeedback360s')]
    private ?Template360 $baseTemplate = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->observation360s = new ArrayCollection();
    }

    public function isReadyToStart(): bool
    {
        if($this->company->getConfig()->isFdb360askPanelToHierarchy()) {
            $allowedStartState = self::STATE_PROP_HI_CLOSED;
        }elseif($this->company->getConfig()->isFdb360askPanelToEvalue()) {
            $allowedStartState = self::STATE_PROP_EV_CLOSED;
        }else{
            $allowedStartState = self::STATE_DRAFT;
        }
        if($this->observation360s->isEmpty()){
            return false;
        }
        return ($this->currentState === $allowedStartState) && $this->arePanelsValid();
    }

    public function arePanelsValid(): bool
    {
        foreach ($this->observation360s as $obs) {
            if ($obs->getState() === Observation360::STATE_READY && !$obs->isPanelSizeValid()) {
                return false;
            }
        }
        return true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return  $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getCompany(): ?company
    {
        return $this->company;
    }

    public function setCompany(?company $company): static
    {
        $this->company = $company;

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

    public function getBeginAt(): ?\DateTimeImmutable
    {
        return $this->beginAt;
    }

    public function setBeginAt(?\DateTimeImmutable $beginAt): static
    {
        if ($beginAt && $this->endAt && $beginAt > $this->endAt) {
            throw new \InvalidArgumentException('Begin date cannot be after end date.');
        }
        if ($beginAt && $this->panelProposalEvalueClosedAt && $beginAt < $this->panelProposalEvalueClosedAt) {
            throw new \InvalidArgumentException('Begin date cannot be before panel proposal closed date.');
        }
        $this->beginAt = $beginAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): static
    {
        if ($endAt && $this->beginAt && $endAt < $this->beginAt) {
            throw new \InvalidArgumentException('End date cannot be before begin date.');
        }
        if ($endAt && $this->panelProposalEvalueClosedAt && $endAt < $this->panelProposalEvalueClosedAt) {
            throw new \InvalidArgumentException('End date cannot be before panel proposal closed date.');
        }
        $this->endAt = $endAt;

        return $this;
    }

    public function getType(): string
    {
        return "Feedback360";
    }

    public function getPanelProposalOpenedAt(): ?\DateTimeImmutable
    {
        return $this->panelProposalOpenedAt;
    }

    public function setPanelProposalOpenedAt(?\DateTimeImmutable $panelProposalOpenedAt): static
    {
        if ($panelProposalOpenedAt){
            $this->addFeature(self::FEATURE_PANEL_EVALUE_PROPOSAL);
        }else{
            $this->removeFeature(self::FEATURE_PANEL_EVALUE_PROPOSAL);
        }
        $this->panelProposalOpenedAt = $panelProposalOpenedAt;
        return $this;
    }

    public function getPanelProposalEvalueClosedAt(): ?\DateTimeImmutable
    {
        return $this->panelProposalEvalueClosedAt;
    }

    public function setPanelProposalEvalueClosedAt(?\DateTimeImmutable $panelProposalEvalueClosedAt): static
    {
        if(!$this->panelProposalOpenedAt) {
            throw new \InvalidArgumentException('Panel proposal opened date must be set before closing it.');
        }

        if ($this->panelProposalEvalueClosedAt < $this->panelProposalOpenedAt) {
            throw new \InvalidArgumentException('Panel proposal closed date cannot be before opened date.');
        }
        $this->panelProposalEvalueClosedAt = $panelProposalEvalueClosedAt;

        return $this;
    }

    public function getCurrentState(): ?string
    {
        return $this->currentState;
    }

    public function setCurrentState(string $currentState): static
    {
        if (!in_array($currentState, self::STATES)) {
            throw new \InvalidArgumentException('Invalid state: ' . $currentState);
        }
        if ($this->isStateAfter($currentState)){
            throw new \InvalidArgumentException('Cannot set state to ' . $currentState . ' as it is before the current state: ' . $this->currentState);
        }
        $this->currentState = $currentState;
        return $this;
    }

    public function isStateOrAfter(string $state): bool
    {
        return array_search($this->currentState, self::STATES) >= array_search($state, self::STATES);
    }

    public function isStateOrBefore(string $state): bool
    {
        return array_search($this->currentState, self::STATES) <= array_search($state, self::STATES);
    }
    public function isStateAfter(string $state): bool
    {
        return array_search($this->currentState, self::STATES) > array_search($state, self::STATES);
    }

    public function isStateBefore(string $state): bool
    {
        return array_search($this->currentState, self::STATES) < array_search($state, self::STATES);
    }

    public function getReportValidationDeadline(): ?\DateTimeImmutable
    {
        return $this->reportValidationDeadline;
    }

    public function setReportValidationDeadline(?\DateTimeImmutable $reportValidationDeadline): static
    {
        if ($reportValidationDeadline < $this->endAt) {
            throw new \InvalidArgumentException('La date limite de validation du rapport ne peut pas être avant la date de fin.');
        }

        $this->reportValidationDeadline = $reportValidationDeadline;

        return $this;
    }

    public function getEvalProgess(): ?float
    {
        return ($this->getEvalFinished() / $this->getEvalTotal()) ?? 0;
    }

    public function getEvalFinished(): ?float
    {
        return null;
    }

    public function getEvalTotal(): ?float
    {
        return null;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }
    
    public function addFeature(string $feature): static
    {
        if (!in_array($feature, self::FEATURES)) {
            throw new \InvalidArgumentException('Feature not available: ' . $feature);
        }
        if (!in_array($feature, $this->features)) {
            $this->features[] = $feature;
        }
        return $this;
    }

    public function removeFeature(string $feature): static
    {
        if (($key = array_search($feature, $this->features)) !== false) {
            unset($this->features[$key]);
        }
        return $this;
    }

    public function haveFeature(string $feature): bool
    {
        return in_array($feature, $this->features);
    }

    public function getPanelProposalHierarchyClosedAt(): ?\DateTimeImmutable
    {
        return $this->panelProposalHierarchyClosedAt;
    }

    public function setPanelProposalHierarchyClosedAt(?\DateTimeImmutable $panelProposalHierarchyClosedAt): static
    {
        if ($panelProposalHierarchyClosedAt){
            $this->addFeature(self::FEATURE_PANEL_HIERARCHY_PROPCONF);
        }else{
            $this->removeFeature(self::FEATURE_PANEL_HIERARCHY_PROPCONF);
        }
        $this->panelProposalHierarchyClosedAt = $panelProposalHierarchyClosedAt;
        return $this;
    }

    public function autoUpdateState(){
        foreach ($this->getObservation360s() as $obs) {
            $obs->autoUpdateState();
            if($obs->isStateOrBefore(Observation360::STATE_READY)){
                $obs->setState(Observation360::STATE_OPEN);
            }else{
                throw new \LogicException('All observations must be at least in READY state to auto update campaign state.');
            }
        }

        if($this->currentState == self::STATE_CONF){
            if($this->name !== null && $this->message !== null)
                $this->setCurrentState(self::STATE_DRAFT);
        }
        if($this->currentState == self::STATE_DRAFT){
            if($this->haveFeature(self::FEATURE_PANEL_EVALUE_PROPOSAL)){
                if($this->panelProposalOpenedAt !== null
                    && date_diff($this->panelProposalOpenedAt, new \DateTimeImmutable())->invert == 0)
                        $this->setCurrentState(self::STATE_PROP_OPEN);
            }  elseif ($this->haveFeature(self::FEATURE_PANEL_HIERARCHY_PROPCONF)) {
                if($this->panelProposalOpenedAt !== null
                    && date_diff($this->panelProposalOpenedAt, new \DateTimeImmutable())->invert == 0)
                        $this->setCurrentState(self::STATE_PROP_EV_CLOSED);
            } else {
                // Action manuelle
            }
        }
        if($this->currentState == self::STATE_PROP_OPEN){
            if($this->haveFeature(self::FEATURE_PANEL_EVALUE_PROPOSAL) || $this->haveFeature(self::FEATURE_PANEL_HIERARCHY_PROPCONF)){
                if ($this->panelProposalEvalueClosedAt !== null 
                    && date_diff($this->panelProposalEvalueClosedAt, new \DateTimeImmutable())->invert == 0){
                        $this->setCurrentState(self::STATE_PROP_EV_CLOSED);
                }
            }else{
                $this->currentState = self::STATE_DRAFT; // Autocorrect impossible state
                $this->autoUpdateState(); // Recheck state
            }
        }
        if($this->currentState == self::STATE_PROP_EV_CLOSED){
            if($this->haveFeature(self::FEATURE_PANEL_HIERARCHY_PROPCONF) && $this->panelProposalHierarchyClosedAt !== null){
                if(date_diff($this->panelProposalHierarchyClosedAt, new \DateTimeImmutable())->invert == 0){
                    $this->setCurrentState(self::STATE_PROP_HI_CLOSED);
                }
            }
        }
        if($this->currentState == self::STATE_PROP_HI_CLOSED){
            // Action manuelle 
        }
        if($this->currentState == self::STATE_READY){
            if($this->beginAt !== null && date_diff($this->beginAt, new \DateTimeImmutable())->invert == 0){
                $this->setCurrentState(self::STATE_ANS_OPEN);
            }
        } 
        if($this->currentState == self::STATE_ANS_OPEN){
            if($this->endAt !== null && date_diff($this->endAt, new \DateTimeImmutable())->invert == 0){
                $this->setCurrentState(self::STATE_ANS_CLOSED);
            }
        }
        if($this->currentState == self::STATE_ANS_CLOSED){
            if($this->reportValidationDeadline !== null && date_diff($this->reportValidationDeadline, new \DateTimeImmutable())->invert == 0){
                $this->setCurrentState(self::STATE_CONF);
            }
        }            
    }

    public function startCampaign(): static
    {
        if ($this->currentState !== self::STATE_PROP_HI_CLOSED) {
            throw new \InvalidArgumentException('Cannot start campaign, current state is: ' . $this->currentState);
        }
        $this->setCurrentState(self::STATE_READY);
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
            $observation360->setCampaign($this);
        }

        return $this;
    }

    public function removeObservation360(Observation360 $observation360): static
    {
        if ($this->observation360s->removeElement($observation360)) {
            // set the owning side to null (unless already changed)
            if ($observation360->getCampaign() === $this) {
                $observation360->setCampaign(null);
            }
        }

        return $this;
    }

    public function getBaseTemplate(): ?Template360
    {
        return $this->baseTemplate;
    }

    public function setBaseTemplate(?Template360 $baseTemplate): static
    {
        $this->baseTemplate = $baseTemplate;

        return $this;
    }
}
