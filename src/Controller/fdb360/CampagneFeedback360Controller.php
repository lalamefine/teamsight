<?php

namespace App\Controller\fdb360;

use App\Abstraction\AbstractCompanyController;
use App\Entity\Feedback360\CampaignFeedback360;
use App\Entity\WebUser;
use App\Form\Type\CampagneFeedback360Type;
use App\Repository\Feedback360\ObserverRepository;
use App\Repository\WebUserRepository;
use App\Service\Obs360Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CampagneFeedback360Controller extends AbstractCompanyController
{
    #[Route('/campagne/manage/dfb360/new', name: 'app_campagne_manage_fdb360_new')]
    public function create(Request $request): Response
    {
        $campagne = new CampaignFeedback360();
        $form = $this->createForm(CampagneFeedback360Type::class, $campagne);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $campagne->setCompany($this->getCompany());
            $this->em->persist($campagne);
            $this->em->flush();
            $this->em->refresh($campagne);
            $this->addFlash('success', 'La campagne de feedback 360 a été créée avec succès.');
            return $this->redirectToRoute('app_campagne_manage_fdb360_edit', ['campagne' => $campagne->getId()]);
        }
        return $this->render('campagne/fdb360/fdb360.html.twig', [
            'company' => $this->getCompany(),
            'campagne' => null,
            'form' => $form
        ]);
    }

    #[Route('/campagne/manage/dfb360/{campagne}', name: 'app_campagne_manage_fdb360_edit', requirements: ['campagne' => '\d+'])]
    public function edit(CampaignFeedback360 $campagne, Request $request): Response
    {
        $form = $this->createForm(CampagneFeedback360Type::class, $campagne);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $campagne->autoUpdateState();
            $campagne->setCompany($this->getCompany());
            $this->em->persist($campagne);
            $this->em->flush();
            $this->addFlash('success', 'La campagne de feedback 360 a été mise à jour avec succès.');
            return $this->redirectToRoute('app_campagne_manage_fdb360_edit', ['campagne' => $campagne->getId()]);
        }
        return $this->render('campagne/fdb360/fdb360.html.twig', [
            'company' => $this->getCompany(),
            'campagne' => $campagne,
            'form' => $form
        ]);
    }

    #[Route('/campagne/manage/dfb360/{campagne}/user/select', name: 'campagne_manage_select_user', requirements: ['campagne' => '\d+'])]
    public function selectUserModal(CampaignFeedback360 $campagne, Request $request, WebUserRepository $webUserRepository): Response
    {
        if (!$campagne->isStateBefore(CampaignFeedback360::STATE_ANS_CLOSED)) {
            $this->addFlash('error', 'La campagne de feedback 360 n\'est pas modifiable.');
            return $this->redirectToRoute('app_campagne_manage_fdb360_edit', ['campagne' => $campagne->getId()]);
        }
        if($request->query->has('search')) {
            $search = $request->query->get('search', '');
            $users = $webUserRepository->search($this->getCompany(), $search);
            return $this->render('generic/userSearch.html.twig', [
                'webusers' => $users,
            ]);
        }

        return $this->render('campagne/fdb360/addUserModal.html.twig', [
            'company' => $this->getCompany(),
            'campagne' => $campagne
        ]);
    }

    #[Route('/campagne/manage/dfb360/{campagne}/user/add', name: 'campagne_manage_add_user', requirements: ['campagne' => '\d+'])]
    public function addUser(CampaignFeedback360 $campagne, Request $request, WebUserRepository $webUserRepository, Obs360Service $obs360Service): Response
    {
        if (!$campagne->isStateBefore(CampaignFeedback360::STATE_ANS_CLOSED)) {
            $this->addFlash('error', 'La campagne de feedback 360 n\'est pas modifiable.');
            return $this->redirectToRoute('app_campagne_manage_fdb360_edit', ['campagne' => $campagne->getId()]);
        }
        if ($request->request->all()['userIds'] ?? null) {
            $userIds = $request->request->all()['userIds'];
            $users = $webUserRepository->findByIdIn($this->getCompany(), $userIds);
            foreach ($users as $user) {
                $obs360Service->startObservation($campagne, $user);
            }
            $this->em->flush();
        }
        return $this->redirectToRoute('app_campagne_manage_fdb360_edit', ['campagne' => $campagne->getId()]);
    }

    #[Route('/campagne/manage/dfb360/{campagne}/content', name: 'app_campagne_manage_fdb360_content', requirements: ['campagne' => '\d+'])]
    public function content(CampaignFeedback360 $campagne, ObserverRepository $observerRepository): Response
    {
        return $this->render('campagne/fdb360/campaignContent.html.twig', [
            'campagne' => $campagne,
            'panelsizes' => $observerRepository->countsByObsForCampaign($campagne->getId())
        ]);
    }
    
}
