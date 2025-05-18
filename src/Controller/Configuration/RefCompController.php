<?php

namespace App\Controller\Configuration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RefCompController extends AbstractController
{
    #[Route('/conf/refComp', name: 'app_conf_referentiel_comp')]
    public function index(): Response
    {
        return $this->render('configuration/referentielComp/index.html.twig', []);
    }
}
