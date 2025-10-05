<?php

namespace App\Controller;

use App\Abstraction\AbstractCompanyController;
use App\Abstraction\CampaignInterface;
use App\Repository\Feedback360\CampaignFeedback360Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CampaignController extends AbstractCompanyController
{
    #[Route('/campaign', name: 'app_campaign')]
    public function index(Request $request, CampaignFeedback360Repository $campaignFeedback360Repository): Response
    {
        /** @var CampaignInterface[] */
        $campaigns = [];
        $filter = $request->query->get('filter', 'all');
        if($this->isGranted('ROLE_T_360_MAN') && ($filter === 'all' || $filter === '360')) {
            $campaignsfdb360 = $campaignFeedback360Repository->findBy(['company' => $this->getCompany()], ['beginAt' => 'DESC', 'createdAt' => 'DESC']);
            $campaigns = array_merge($campaigns, $campaignsfdb360);
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

        return $this->render('campaign/index.html.twig', [
            'campaigns' => $campaigns,
            'filter' => $filter
        ]);
    }

    #[Route('/campaign/new', name: 'app_campaign_new_generic')]
    public function selectType(): Response
    {
        return $this->render('campaign/picktype.html.twig', [ ]);
    }
    
}
