<?php

namespace App\Controller\Configuration;

use App\Entity\WebUser;
use App\Form\Type\WebUserType;
use App\Repository\WebUserRepository;
use App\Service\UserUserAccessService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/gestion/company/users/webedit/list', name: 'app_gestion_company_users_list', methods: ['GET'])]
    public function list(Request $request, WebUserRepository $webUserRepository, #[CurrentUser] WebUser $curUser): Response
    {
        $offset = $request->query->getInt('p', 1) - 1;
        $pageLength = 100;

        $criterias = Criteria::create();
        $criterias->orderBy(['lastName' => Order::Ascending]);
        $criterias->andWhere(Criteria::expr()->eq('company', $curUser->getCompany()));
        $criterias->andWhere(Criteria::expr()->eq('displayed', true));
        if($request->query->has('search')) {
            $search = $request->query->get('search');
            $criterias->andWhere(Criteria::expr()->orX(
                Criteria::expr()->contains('lastName', $search),
                Criteria::expr()->contains('email', $search),
                Criteria::expr()->contains('companyInternalID', $search),
            ));
        }

        $total = $webUserRepository->matching($criterias)->count();
        $users = $webUserRepository->matching($criterias)->slice($offset*$pageLength, $pageLength);

        return $this->render('configuration/companyUsers/webui/list.html.twig', [
            'users' => $users,
            'currentpage' => $offset + 1,
            'lastpage' => ceil($total / $pageLength),
        ]);
    }

    
    #[Route('/gestion/company/users/webedit/{userid}', name: 'app_gestion_company_users_edit', methods: ['GET', 'POST'])]
    public function edit(string|int $userid, Request $request, WebUserRepository $webUserRepository, #[CurrentUser] WebUser $curUser, EntityManagerInterface $em): Response
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

        $form = $this->createForm(WebUserType::class, $targetuser, );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($targetuser);
            $em->flush();
            $this->addFlash('success', 'User updated successfully.');
            return $this->redirectToRoute('app_gestion_company_users');
        }

        return $this->render('configuration/companyUsers/webui/edit.html.twig', [
            'targetuser' => $targetuser,
            'form' => $form
        ]);
    }

    #[Route('/gestion/company/users/webedit/disable/{targetuser}', name: 'app_gestion_company_users_delete', requirements: ['targetuser' => '\d+'])]
    public function delete(WebUser $targetuser, Request $request, EntityManagerInterface $em): Response
    {
        if($targetuser === null)
            throw $this->createNotFoundException('User not found');
        if (!$this->userUserAccessService->canEditUser($targetuser))
            throw $this->createAccessDeniedException('You do not have permission to edit this user');

        if ($request->isMethod('POST') && $request->request->get('confirm', 'no') == 'yes') {
            $targetuser->setDisplayed(false);
            $targetuser->setCanConnect(false);
            $em->persist($targetuser);
            $em->flush();
            $this->addFlash('success', 'User disabled successfully.');
            return $this->redirectToRoute('app_gestion_company_users');
        }else{
            return $this->render('configuration/companyUsers/webui/delete.html.twig', [
                'targetuser' => $targetuser,
            ]);
        }
    }
}
