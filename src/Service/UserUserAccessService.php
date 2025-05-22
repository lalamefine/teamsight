<?php 

namespace App\Service;

use App\Abstraction\CompanyUserInterface;
use App\Entity\WebUser;
use App\Repository\WebUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserUserAccessService
{
    public ?CompanyUserInterface $currentUser = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly WebUserRepository $webUserRepository,
        private readonly Security $security
    ) { 
        $this->currentUser = $this->security->getUser();
    }

    public function canEditUser(WebUser $subject): bool
    {
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }
        if ($this->security->isGranted('ROLE_ENT_USER_MANAGER') || $this->security->isGranted('ROLE_CAMP_MAKR')) {
            return $this->currentUser->getCompany()->getId() === $subject->getCompany()->getId();
        }
        return false;
    }

}