<?php

namespace App\Controller\Configuration;

use App\Abstraction\AbstractCompanyController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RefCompController extends AbstractCompanyController
{
    #[Route('/cf/refComp', name: 'app_conf_referentiel_comp')]
    public function index(): Response
    {
        return $this->render('configuration/referentielComp/index.html.twig', []);
    }
}
