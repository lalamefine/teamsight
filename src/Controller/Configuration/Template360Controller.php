<?php

namespace App\Controller\Configuration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Template360Controller extends AbstractController
{
    #[Route('/conf/template360', name: 'app_conf_templates_360')]
    public function index(): Response
    {
        return $this->render('configuration/template360/index.html.twig', []);
    }
}
