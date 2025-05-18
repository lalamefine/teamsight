<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_landing')]
    public function index(): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->dashboard();
        }

        return $this->landing();
    }

    private function landing(): Response
    {
        return $this->render('landing/index.html.twig', []);
    }

    private function dashboard(): Response
    {
        return $this->render('dashboard/index.html.twig', []);
    }
}
