<?php 
namespace App\Abstraction;

use App\Entity\Company;
use Symfony\Component\Security\Core\User\UserInterface;

interface CompanyUserInterface extends UserInterface
{
    public function getCompany(): ?Company;
}