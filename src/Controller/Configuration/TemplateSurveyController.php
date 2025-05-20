<?php

namespace App\Controller\Configuration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TemplateSurveyController extends AbstractController
{
    #[Route('/cf/templateSurvey', name: 'app_conf_templates_survey')]
    public function index(): Response
    {
        return $this->render('configuration/templateSurvey/index.html.twig', []);
    }
}
