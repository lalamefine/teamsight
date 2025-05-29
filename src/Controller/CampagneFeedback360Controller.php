<?php

namespace App\Controller;

use App\Abstraction\AbstractCompanyController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CampagneFeedback360Controller extends AbstractCompanyController
{
    #[Route('/campagne/manage/dfb360/{id}', name: 'app_campagne_manage_fdb360', defaults: ['id' => 'new'])]
    public function edit(string $id): Response
    {
        if ($id === 'new') {
            // Logic to create a new campaign
            // This could involve initializing a new CampaignFeedback360 entity
            // and passing it to the form for creation.
        } else {
            // Logic to edit an existing campaign
            // This would typically involve fetching the CampaignFeedback360 entity by ID.
        }

        return $this->render('campagne/manage/fdb360.html.twig', [
            'company' => $this->getCompany(),
        ]);
    }
    
}
