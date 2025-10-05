<?php

namespace App\Controller\fdb360;

use App\Abstraction\AbstractCompanyController;
use App\Entity\Feedback360\CampaignFeedback360;
use App\Entity\WebUser;
use App\Form\Type\CampaignFeedback360Type;
use App\Repository\Feedback360\ObserverRepository;
use App\Repository\WebUserRepository;
use App\Service\Obs360Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CampaignFeedback360Controller extends AbstractCompanyController
{
    #[Route('/camp/fb360/new', name: 'app_campaign_manage_fdb360_new')]
    public function create(Request $request): Response
    {
        $campaign = new CampaignFeedback360();
        $form = $this->createForm(CampaignFeedback360Type::class, $campaign);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $campaign->setCompany($this->getCompany());
            $this->em->persist($campaign);
            $this->em->flush();
            $this->em->refresh($campaign);
            $this->addFlash('success', 'La campagne de feedback 360 a été créée avec succès.');
            return $this->redirectToRoute('app_campaign_manage_fdb360_edit', ['campaign' => $campaign->getId()]);
        }
        return $this->render('campaign/fdb360/fdb360.html.twig', [
            'company' => $this->getCompany(),
            'campaign' => null,
            'form' => $form
        ]);
    }

    #[Route('/camp/fb360/{campaign}', name: 'app_campaign_manage_fdb360_edit', requirements: ['campaign' => '\d+'])]
    public function edit(CampaignFeedback360 $campaign, Request $request): Response
    {
        $form = $this->createForm(CampaignFeedback360Type::class, $campaign);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $campaign->autoUpdateState();
            $campaign->setCompany($this->getCompany());
            $this->em->persist($campaign);
            $this->em->flush();
            $this->addFlash('success', 'La campagne de feedback 360 a été mise à jour avec succès.');
            return $this->redirectToRoute('app_campaign_manage_fdb360_edit', ['campaign' => $campaign->getId()]);
        }
        return $this->render('campaign/fdb360/fdb360.html.twig', [
            'company' => $this->getCompany(),
            'campaign' => $campaign,
            'form' => $form
        ]);
    }

    #[Route('/camp/fb360/{campaign}/user/select', name: 'campaign_manage_select_user', requirements: ['campaign' => '\d+'])]
    public function selectUserModal(CampaignFeedback360 $campaign, Request $request, WebUserRepository $webUserRepository): Response
    {
        if (!$campaign->isStateBefore(CampaignFeedback360::STATE_ANS_CLOSED)) {
            $this->addFlash('error', 'La campagne de feedback 360 n\'est pas modifiable.');
            return $this->redirectToRoute('app_campaign_manage_fdb360_edit', ['campaign' => $campaign->getId()]);
        }
        if($request->query->has('search')) {
            $search = $request->query->get('search', '');
            $users = $webUserRepository->search($this->getCompany(), $search);
            return $this->render('generic/userSearch.html.twig', [
                'webusers' => $users,
            ]);
        }

        return $this->render('campaign/fdb360/addUserModal.html.twig', [
            'company' => $this->getCompany(),
            'campaign' => $campaign
        ]);
    }

    #[Route('/camp/fb360/{campaign}/user/add', name: 'campaign_manage_add_user', requirements: ['campaign' => '\d+'])]
    public function addUser(CampaignFeedback360 $campaign, Request $request, WebUserRepository $webUserRepository, Obs360Service $obs360Service): Response
    {
        if (!$campaign->isStateBefore(CampaignFeedback360::STATE_ANS_CLOSED)) {
            $this->addFlash('error', 'La campagne de feedback 360 n\'est pas modifiable.');
            return $this->redirectToRoute('app_campaign_manage_fdb360_edit', ['campaign' => $campaign->getId()]);
        }
        if ($request->request->get('userId') ?? null) {
            $userId = $request->request->get('userId');
            $users = $webUserRepository->findByIdIn($this->getCompany(), [$userId]);
            foreach ($users as $user) {
                $obs360Service->startObservation($campaign, $user);
            }
            $this->em->flush();
        }
        return $this->redirectToRoute('app_campaign_manage_fdb360_edit', ['campaign' => $campaign->getId()]);
    }

    #[Route('/camp/fb360/{campaign}/content', name: 'app_campaign_manage_fdb360_content', requirements: ['campaign' => '\d+'])]
    public function content(CampaignFeedback360 $campaign, ObserverRepository $observerRepository): Response
    {
        return $this->render('campaign/fdb360/campaignContent.html.twig', [
            'campaign' => $campaign,
            'panelsizes' => $observerRepository->countsByObsForCampaign($campaign->getId())
        ]);
    }

    #[Route('/camp/fb360/{campaign}/start', name: 'app_campaign_manage_fdb360_start', requirements: ['campaign' => '\d+'])]
    public function start(CampaignFeedback360 $campaign): Response
    {
        if (!$campaign->isReadyToStart()) {
            $this->addFlash('error', 'Action invalide.');
            return $this->redirectToRoute('app_campaign_manage_fdb360_edit', ['campaign' => $campaign->getId()]);
        }
        if($campaign->getBeginAt() <= new \DateTimeImmutable()){
            $campaign->setBeginAt(new \DateTimeImmutable());
            $this->addFlash('success', 'Les personnes sollicitées peuvent maintenant répondre aux questionnaires.');
            $campaign->setCurrentState(CampaignFeedback360::STATE_ANS_OPEN);
        }else{
            $this->addFlash('success', 'La campaign de feedback 360 est prête à démarrer à la date prévue.');
            $campaign->setCurrentState(CampaignFeedback360::STATE_READY);
        }
        if($campaign->getEndAt() == null){
            $campaign->setEndAt($campaign->getBeginAt()->modify('+30 days'));
        }

        $this->em->persist($campaign);
        $this->em->flush();
        return $this->redirectToRoute('app_campaign_manage_fdb360_edit', ['campaign' => $campaign->getId()]);
    }

    #[Route('/camp/fb360/{campaign}/open-proposal', name: 'app_campaign_manage_fdb360_open_proposal', requirements: ['campaign' => '\d+'])]
    public function openProposal(CampaignFeedback360 $campaign, Request $request): Response
    {
        if (!$campaign->getCurrentState() != CampaignFeedback360::STATE_DRAFT) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('app_campaign_manage_fdb360_edit', ['campaign' => $campaign->getId()]);
        }
        if($request->query->has('cloture-obs') || $request->query->get('cloture-hierarchie') ){
            $campaign->setCurrentState(CampaignFeedback360::STATE_PROP_OPEN);
            $campaign->setPanelProposalOpenedAt(new \DateTimeImmutable());
            if($request->query->has('cloture-obs')){
                $campaign->setPanelProposalEvalueClosedAt(new \DateTimeImmutable($request->query->get('cloture-obs')));
            }
            if($request->query->has('cloture-hierarchie')){
                $campaign->setPanelProposalHierarchyClosedAt(new \DateTimeImmutable($request->query->get('cloture-hierarchie')));
            }


            $this->em->persist($campaign);
            $this->em->flush();
            $this->addFlash('success', 'La campagne de feedback 360 est maintenant en état "Proposition ouverte".');
            return $this->redirectToRoute('app_campaign_manage_fdb360_edit', ['campaign' => $campaign->getId()]);
        }else{
            return $this->render('campaign/fdb360/confirmOpenProposal.html.twig', [
                'campaign' => $campaign
            ]);
        }



    }
}
