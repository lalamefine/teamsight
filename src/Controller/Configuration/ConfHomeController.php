<?php

namespace App\Controller\Configuration;

use App\Entity\WebUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ConfHomeController extends AbstractController
{
    #[Route('/conf', name: 'app_conf')]
    public function index(#[CurrentUser()] WebUser $webuser): Response
    {
        return $this->render('configuration/confHome/index.html.twig', [
            'userCompanyConfig' => $webuser->getCompany()?->getConfig(),
        ]);
    }
}
