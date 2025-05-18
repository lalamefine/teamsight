<?php

namespace App\Controller\Configuration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TemplateEAController extends AbstractController
{
    #[Route('/conf/templateEA', name: 'app_conf_templates_ea')]
    public function index(): Response
    {
        return $this->render('configuration/templateEA/index.html.twig', []);
    }
}
