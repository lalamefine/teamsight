<?php

use App\Entity\Company;

interface CampaignInterface
{
    public function getId(): ?int;
    public function getCompany(): ?Company;
    public function setCompany(?Company $company): static;
    public function getCreatedAt(): ?\DateTimeImmutable;
    public function setCreatedAt(\DateTimeImmutable $createdAt): static;
    public function getBeginAt(): ?\DateTimeImmutable;
    public function setBeginAt(?\DateTimeImmutable $beginAt): static;
    public function getEndAt(): ?\DateTimeImmutable;
    public function setEndAt(?\DateTimeImmutable $endAt): static;
    public function getType(): ?string;

    public function getEvalProgess(): ?float;
    public function getEvalFinished(): ?float;
    public function getEvalTotal(): ?float;
}