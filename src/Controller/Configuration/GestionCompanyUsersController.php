<?php

namespace App\Controller\Configuration;

use App\Abstraction\AbstractCompanyController;
use App\Entity\WebUser;
use App\Form\Type\WebUserType;
use App\Repository\WebUserRepository;
use App\Service\UserUserAccessService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GestionCompanyUsersController extends AbstractCompanyController
{
    #[Route('/gestion/company/users', name: 'app_gestion_company_users')]
    public function index(): Response
    {
        return $this->render('configuration/companyUsers/index.html.twig', []);
    }

    #[Route('/gestion/company/users/webedit/list', name: 'app_gestion_company_users_list', methods: ['GET'])]
    public function list(Request $request, WebUserRepository $webUserRepository): Response
    {
        $offset = $request->query->getInt('p', 1) - 1;
        $pageLength = 100;

        $criterias = Criteria::create();
        $criterias->orderBy(['lastName' => Order::Ascending]);
        $criterias->andWhere(Criteria::expr()->eq('company', $this->getCompany()));
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
    public function edit(string|int $userid, Request $request, WebUserRepository $webUserRepository, UserUserAccessService $userUserAccessService): Response
    {
        if($userid === 'new') {
            $targetuser = new WebUser();
            $targetuser->setCompany($this->getCompany());
        }elseif(is_numeric($userid)){
            $targetuser = $webUserRepository->find($userid);
        }else
            throw $this->createNotFoundException('Invalid user id');
        if($targetuser === null)
            throw $this->createNotFoundException('User not found');
        if (!$userUserAccessService->canEditUser($targetuser))
            throw $this->createAccessDeniedException('You do not have permission to edit this user');

        $form = $this->createForm(WebUserType::class, $targetuser, );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($targetuser);
            $this->em->flush();
            $this->addFlash('success', 'User updated successfully.');
            return $this->redirectToRoute('app_gestion_company_users');
        }

        return $this->render('configuration/companyUsers/webui/edit.html.twig', [
            'targetuser' => $targetuser,
            'form' => $form
        ]);
    }

    #[Route('/gestion/company/users/webedit/disable/{targetuser}', name: 'app_gestion_company_users_delete', requirements: ['targetuser' => '\d+'])]
    public function delete(WebUser $targetuser, Request $request, UserUserAccessService $userUserAccessService): Response
    {
        if($targetuser === null)
            throw $this->createNotFoundException('User not found');
        if (!$userUserAccessService->canEditUser($targetuser))
            throw $this->createAccessDeniedException('You do not have permission to edit this user');

        if ($request->isMethod('POST') && $request->request->get('confirm', 'no') == 'yes') {
            $targetuser->setDisplayed(false);
            $targetuser->setCanConnect(false);
            $this->em->persist($targetuser);
            $this->em->flush();
            $this->addFlash('success', 'User disabled successfully.');
            return $this->redirectToRoute('app_gestion_company_users');
        }else{
            return $this->render('configuration/companyUsers/webui/delete.html.twig', [
                'targetuser' => $targetuser,
            ]);
        }
    }

    // CSV
    #[Route('/gestion/company/users/csv/template', name: 'company_users_csv_template', methods: ['GET'])] 
    public function csvTemplate(): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="users_template_utf8.csv"');

        $csvData = "nom*,prénom*,email*,rôles (séparateur : '+'),id ".$this->getCompany()->getName().",métier,équipe\n";

        return $response->setContent($csvData);
    }

    #[Route('/gestion/company/users/csv/export', name: 'company_users_csv_export', methods: ['GET'])]
    public function csvExport(WebUserRepository $webUserRepository): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="users_utf8.csv"');

        $csvData = "nom*,prénom*,email*,rôles (séparateur : '+'),id ".$this->getCompany()->getName().",métier,équipe\n";

        $users = $webUserRepository->findBy(['company' => $this->getCompany(), 'displayed' => true]);
        foreach ($users as $user) {
            $id = $this->getCompany()->getConfig()->getAgtIdType() == 'company ' ? $user->getCompanyInternalID() : '';
            $roles = implode('+', array_map(fn($role) => str_replace('ROLE_', '', $role), $user->getRoles()));
            $csvData .= sprintf("%s,%s,%s,%s,%s\n",
                $user->getLastName(),
                $user->getFirstName(),
                $user->getEmail(),
                $roles,
                $id,
                $user->getJob() ?? '',
                $user->getTeam() ?? ''
            );
        }
        return $response->setContent($csvData);
    }

    #[Route('/gestion/company/users/csv/import', name: 'company_users_csv_import', methods: ['POST'])]
    public function csvImport(Request $request, EntityManagerInterface $em): Response
    {
        $confirmImport = $request->request->get('confimImport', 'false') == 'true';
        $clearBeforeImport = $request->request->get('clearBeforeImport', 'false') == 'true';
        if ($confirmImport){
            $tableName = "_tmp_user_import_".$this->getCompany()->getId();
            $cid = $this->getCompany()->getId();
            if ($clearBeforeImport) {
                $em->getConnection()->executeQuery("UPDATE web_user SET displayed = 0, can_connect=0 WHERE company_id = $cid");
            }
            $em->getConnection()->executeQuery("INSERT INTO web_user
                (last_name, first_name, email, company_id, company_internal_id, roles, displayed, can_connect, job, team)
                SELECT last_name, first_name, email, $cid, company_internal_id, roles, true, true, job, team FROM $tableName
                ON CONFLICT (" .  match($this->getCompany()->getConfig()->getAgtIdType()) {
                    'company' => "company_internal_id, company_id",
                    'email' => "email, company_id",
                }  . ")
                DO UPDATE SET
                    last_name = EXCLUDED.last_name,
                    first_name = EXCLUDED.first_name,
                    email = EXCLUDED.email,
                    roles = EXCLUDED.roles,
                    company_internal_id = EXCLUDED.company_internal_id,
                    displayed = true,
                    can_connect = true,
                    job = EXCLUDED.job,
                    team = EXCLUDED.team
                    ;");
            $em->getConnection()->executeQuery("DROP TABLE IF EXISTS $tableName");

            $this->addFlash('success', 'Users imported successfully.'); 
            return $this->redirectToRoute('app_gestion_company_users', [
                'tab' => 'CSV',
            ]);
        } else {
            $file = $request->files->get('csvFile');
            if (!$file || !$file->isValid()) {
                $this->addFlash('error', 'Invalid file upload.');
                return $this->redirectToRoute('app_gestion_company_users', [
                    'tab' => 'CSV',
                ]);
            }

            $handle = fopen($file->getPathname(), 'r');
            if ($handle === false) {
                $this->addFlash('error', 'Failed to open the file.');
                return $this->redirectToRoute('app_gestion_company_users', [
                    'tab' => 'CSV',
                ]);
            }

            // Skip header
            fgetcsv($handle);
            $tableName = "_tmp_user_import_".$this->getCompany()->getId();
            $em->getConnection()->executeQuery("DROP TABLE IF EXISTS $tableName;");
            $em->getConnection()->executeQuery("
                CREATE TABLE $tableName (
                    last_name VARCHAR(64) NOT NULL,
                    first_name VARCHAR(64) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    company_internal_id VARCHAR(255) NOT NULL,
                    roles TEXT NOT NULL,
                    job VARCHAR(128),
                    team VARCHAR(128),
                    PRIMARY KEY (email)
            );");
            $baseSql = "INSERT INTO $tableName (last_name, first_name, email, roles, company_internal_id, job, team) VALUES ";
            $params = [];
            $i = 0;
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) < 4) {
                    continue; // Skip invalid rows
                }
                if (strlen(trim($data[4])) === 0) {
                    $roles = ['ROLE_USER']; // Default role if none provided
                }else{
                    $roles = array_map(
                        fn ($r) => 'ROLE_'.trim($r), 
                        explode('+', $data[3])
                    );
                }
                $params = array_merge($params,[
                    $data[0],
                    $data[1],
                    $data[2],
                    implode(',', $roles),
                    $data[4] ?? null,
                    $data[5] ?? null,
                    $data[6] ?? null
                ]);
                $i += 1;
                if ($i > 1000) {
                    $selectors = implode(",",array_fill(0, $i, '(?, ?, ?, ?, ?, ?, ?)'));
                    $em->getConnection()->executeQuery($baseSql . $selectors, $params);
                    $params = [];
                    $i = 0;
                }
            }
            $selectors = implode(",",array_fill(0, $i, '(?, ?, ?, ?, ?, ?, ?)'));
            $em->getConnection()->executeQuery($baseSql . $selectors, $params);     
            fclose($handle);
            return $this->render('configuration/companyUsers/csv.html.twig', [
                'total' => $em->getConnection()->fetchOne("SELECT COUNT(*) FROM $tableName"),   
                'first100' => !$confirmImport ? $em->getConnection()->fetchAllAssociative("SELECT * FROM $tableName LIMIT 100") : null,                        
            ]);
        }
    }


}
