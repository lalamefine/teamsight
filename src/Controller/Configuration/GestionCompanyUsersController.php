<?php

namespace App\Controller\Configuration;

use App\Entity\WebUser;
use App\Repository\WebUserRepository;
use App\Service\UserUserAccessService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GestionCompanyUsersController extends AbstractController
{
    public function __construct(
        private UserUserAccessService $userUserAccessService,
    ) { }

    #[Route('/gestion/company/users', name: 'app_gestion_company_users')]
    public function index(): Response
    {
        return $this->render('configuration/companyUsers/index.html.twig', [
            
        ]);
    }

    
    #[Route('/gestion/company/users/webedit/{userid}', name: 'app_gestion_company_users_edit', methods: ['GET', 'POST'])]
    public function edit(string|int $userid, Request $request, WebUserRepository $webUserRepository, #[CurrentUser] WebUser $curUser): Response
    {
        if($userid === 'new') {
            $targetuser = new WebUser();
            $targetuser->setCompany($curUser->getCompany());
        }elseif(is_numeric($userid)){
            $targetuser = $webUserRepository->find($userid);
        }else
            throw $this->createNotFoundException('Invalid user id');
        if($targetuser === null)
            throw $this->createNotFoundException('User not found');
        if (!$this->userUserAccessService->canEditUser($targetuser))
            throw $this->createAccessDeniedException('You do not have permission to edit this user');

        if($request->isMethod('POST')) {
            //TODO: handle form submission
            return $this->redirectToRoute('app_gestion_company_users');
        }

        return $this->render('configuration/companyUsers/webui/edit.html.twig', [
            'targetuser' => $targetuser,
        ]);
    }
}
