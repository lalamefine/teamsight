<?php

namespace App\Controller\Configuration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ConfHomeController extends AbstractController
{
    #[Route('/conf', name: 'app_conf')]
    public function index(): Response
    {
        return $this->render('configuration/confHome/index.html.twig', []);
    }
}
