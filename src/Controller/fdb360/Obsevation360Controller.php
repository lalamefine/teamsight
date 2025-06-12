<?php

namespace App\Controller\fdb360;

use App\Abstraction\AbstractCompanyController;
use App\Entity\Feedback360\Observation360;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Obsevation360Controller extends AbstractCompanyController
{
    #[Route('/obsevation360/{id}/panel', name: 'observation_panel_edit')]
    public function panel(): Response
    {
        return $this->render('campagne\fdb360-obs\panel.html.twig', [
        ]);
    }

    #[Route('/obsevation360/{id}/delete', name: 'observation_delete', methods: ['DELETE'])]
    public function delete(Observation360 $observation360): Response
    {
        $this->em->remove($observation360);
        $this->em->flush();
        return $this->redirectToRoute('app_campagne_manage_fdb360_content', [
            'campagne' => $observation360->getCampaign()->getId(),
        ]);
    }
}
