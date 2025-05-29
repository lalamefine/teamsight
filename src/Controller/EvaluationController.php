<?php

namespace App\Controller;

use App\Abstraction\AbstractCompanyController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EvaluationController extends AbstractCompanyController
{
    #[Route('/evaluation', name: 'app_evaluation')]
    public function index(): Response
    {
        return $this->render('evaluation/index.html.twig', [
            'controller_name' => 'EvaluationController',
        ]);
    }
}
