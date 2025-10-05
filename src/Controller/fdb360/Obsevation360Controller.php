<?php

namespace App\Controller\fdb360;

use App\Abstraction\AbstractCompanyController;
use App\Entity\Feedback360\Observation360;
use App\Entity\Feedback360\Observer;
use App\Repository\Feedback360\ObserverRepository;
use App\Repository\Feedback360\ObsProfileRepository;
use App\Repository\WebUserRepository;
use App\Service\HTMXResponsesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Obsevation360Controller extends AbstractCompanyController
{

    #[Route('/obs360/{id}', name: 'observation_view')]
    public function view(Observation360 $observation): Response
    {
        return $this->render('campaign\fdb360-obs\view.html.twig', [
            'observation' => $observation,
        ]);
    }

    #[Route('/obs360/{id}/panel', name: 'observation_panel_edit')]
    public function panel(Observation360 $observation): Response
    {
        return $this->render('campaign\fdb360-obs\panel.html.twig', [
            'observation' => $observation,
        ]);
    }

    #[Route('/obs360/{id}/delete', name: 'observation_delete', methods: ['DELETE'])]
    public function delete(Observation360 $observation360): Response
    {
        $this->em->remove($observation360);
        $this->em->flush();
        return $this->redirectToRoute('app_campaign_manage_fdb360_content', [
            'campaign' => $observation360->getCampaign()->getId(),
        ]);
    }

    #[Route('/obs360/observers/remove/{id}', name: 'observation_remove_observer', methods: ['DELETE'])]
    public function removeObserver(int $id, ObserverRepository $observerRepository): Response
    {
        $observer = $observerRepository->find($id);
        if (!$observer) {
            throw $this->createNotFoundException('Observer not found');
        }
        $this->em->remove($observer);
        $this->em->flush();
        return new Response('');
    }

    #[Route('/obs360/{id}/observers/add/modal', name: 'observation_add_observer_modal', methods: ['GET'])]
    public function addObserverModal(Observation360 $observation360, Request $request, WebUserRepository $webUserRepository, ObsProfileRepository $obsProfileRepository): Response
    {
        // Search Logic
        if($request->query->has('search')) {
            $search = $request->query->get('search', '');
            $users = $webUserRepository->search($this->getCompany(), $search);
            return $this->render('generic/userSearch.html.twig', [
                'webusers' => $users,
            ]);
        }

        return $this->render('campaign/fdb360-obs/searchAgentModal.html.twig', [
            'profiles' => $obsProfileRepository->findBy([
                'company' => $this->getCompany(),
                'selectableManually' => true,
            ]),
            'observation' => $observation360,
        ]);
    }

    #[Route('/obs360/{id}/observers/add', name: 'observation_add_observer', methods: ['POST'])]
    public function addObserverAction(Observation360 $observation360, Request $request, WebUserRepository $webUserRepository, ObsProfileRepository $obsProfileRepository, HTMXResponsesService $htmx): Response
    {
        if ($request->request->get('userId') ?? null) {
            $userId = $request->request->get('userId');
            $user = $webUserRepository->findOneBy([
                'company' => $this->getCompany(),
                'id' => $userId
            ]);

            $profileId = $request->request->get('profileId');
            $profile = $obsProfileRepository->find($profileId);
            if (!$profile) {
                $this->addFlash('error', 'Profile non trouvé.');
                return $htmx->pageRefresh();
            }
            if($profile->getCompany() !== $this->getCompany()) {
                $this->addFlash('error', 'Vous ne pouvez pas utilliser ce profil pour cette observation.');
                return $htmx->pageRefresh();
            }

            if ($observation360->hasObserver($user)) {
                $this->addFlash('error', "L'utillisateur : " . $user->getFullName() . ' est déjà associé à cette observation.');
                return $htmx->pageRefresh();
            }
            $observer = new Observer($observation360, $user, $profile);
            $observation360->addObserver($observer);
            $this->em->persist($observer);
            $this->em->flush();
        }

        return $this->renderBlock('campaign\fdb360-obs\panel.html.twig', 'obsRow', [
            'observer' => $observer,
            'observation' => $observation360,
        ]);
    }
}
