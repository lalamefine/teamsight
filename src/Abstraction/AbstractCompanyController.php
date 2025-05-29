<?php 

namespace App\Abstraction;

use App\Entity\Company;
use App\Entity\WebUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

abstract class AbstractCompanyController extends AbstractController
{
    public function __construct(
        private Security $security,
        protected EntityManagerInterface $em
    ) { }

    protected function getCompany(): ?Company
    {
        return $this->getUser()->getCompany();
    }

    protected function getUser(): ?WebUser
    {
        return $this->security->getUser();
    }
}