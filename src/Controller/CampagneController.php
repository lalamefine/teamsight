<?php

namespace App\Controller;

use App\Abstraction\AbstractCompanyController;
use App\Abstraction\CampaignInterface;
use App\Repository\Feedback360\CampaignFeedback360Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CampagneController extends AbstractCompanyController
{
    #[Route('/campagne', name: 'app_campagne')]
    public function index(Request $request, CampaignFeedback360Repository $campaignFeedback360Repository): Response
    {
        /** @var CampaignInterface[] */
        $campagnes = [];
        $filter = $request->query->get('filter', 'all');
        if($this->isGranted('ROLE_T_360_MAN') && ($filter === 'all' || $filter === '360')) {
            $campagnesfdb360 = $campaignFeedback360Repository->findBy(['company' => $this->getCompany()], ['beginAt' => 'DESC', 'createdAt' => 'DESC']);
            $campagnes = array_merge($campagnes, $campagnesfdb360);
        }
        if($this->isGranted('ROLE_T_EA_MAN') && ($filter === 'all' || $filter === 'ea')) {
            // TODO
        }
        if($this->isGranted('ROLE_T_REFCOM_MAN') && ($filter === 'all' || $filter === 'refcom')) {
            // TODO
        }
        if($this->isGranted('ROLE_T_ENSURV_MAN') && ($filter === 'all' || $filter === 'ensurv')) {
            // TODO
        }

        return $this->render('campagne/index.html.twig', [
            'campagnes' => $campagnes,
            'filter' => $filter
        ]);
    }

    #[Route('/campagne/new', name: 'app_campagne_new_generic')]
    public function selectType(): Response
    {
        return $this->render('campagne/picktype.html.twig', [ ]);
    }
    
}
