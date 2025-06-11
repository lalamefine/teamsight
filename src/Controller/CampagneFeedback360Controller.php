<?php

namespace App\Controller;

use App\Abstraction\AbstractCompanyController;
use App\Entity\Feedback360\CampaignFeedback360;
use App\Form\Type\CampagneFeedback360Type;
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
        return $this->render('campagne/manage/fdb360.html.twig', [
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
        return $this->render('campagne/manage/fdb360.html.twig', [
            'company' => $this->getCompany(),
            'campagne' => $campagne,
            'form' => $form
        ]);
    }
    
}
