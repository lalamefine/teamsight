<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CampagneController extends AbstractController
{
    #[Route('/campagne', name: 'app_campagne')]
    public function index(): Response
    {
        return $this->render('campagne/index.html.twig', [
            'controller_name' => 'CampagneController',
        ]);
    }
}
