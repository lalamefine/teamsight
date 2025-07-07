<?php

namespace App\Controller\Configuration;

use App\Abstraction\AbstractCompanyController;
use App\Entity\WebUser;
use App\Form\Type\WebUserType;
use App\Repository\WebUserRepository;
use App\Service\UserUserAccessService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;
use Doctrine\ORM\EntityManagerInterface;

final class GestionCompanyUsersController extends AbstractCompanyController
{
    private array $importColumns = [
        'last_name' => 'Nom',
        'first_name' => 'Prénom',
        'email' => 'Email',
        'roles' => 'Rôles (séparateur : \'+\')',
        'company_internal_id' => 'Identifiant d\'entreprise',
        'job' => 'Métier',
        'team' => 'Équipe',
    ];

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

        $form = $this->createForm(WebUserType::class, $targetuser);
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

        $csvData = implode(',', $this->importColumns) . "\n";

        return $response->setContent($csvData);
    }

    #[Route('/gestion/company/users/csv/export', name: 'company_users_csv_export', methods: ['GET'])]
    public function csvExport(): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="users_utf8.csv"');
        $csvData = implode(',', $this->importColumns) . "\n";
        $userRows = $this->em->getConnection()->executeQuery(
            "SELECT ".implode(',', array_keys($this->importColumns))." FROM web_user WHERE company_id = ? AND displayed = true",
            [$this->getCompany()->getId()])->fetchAllAssociative();
        foreach ($userRows as $row) {
            $csvData .= implode(',', $row) . "\n";
        }
        return $response->setContent($csvData);
    }

    #[Route('/gestion/company/users/csv/import', name: 'company_users_csv_import', methods: ['POST'])]
    public function csvImport(Request $request, TokenStorageInterface $tokenStorage): Response
    {
        $baseColumns = implode(',', array_keys($this->importColumns));
        $confirmImport = $request->request->get('confimImport', 'false') == 'true';
        $clearBeforeImport = $request->request->get('clearBeforeImport', 'false') == 'true';
        if ($confirmImport){
            $tableName = "_tmp_user_import_".$this->getCompany()->getId();
            $cid = $this->getCompany()->getId();
            if ($clearBeforeImport) {
                $this->em->getConnection()->executeQuery("UPDATE web_user SET displayed = 0, can_connect=0 WHERE company_id = $cid");
            }
            $this->em->getConnection()->executeQuery("INSERT INTO web_user
                ($baseColumns, company_id, displayed, can_connect)
                SELECT $baseColumns, $cid, true, true FROM $tableName
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
            $this->em->getConnection()->executeQuery("DROP TABLE IF EXISTS $tableName");

            // Reauthenticate the user to refresh the token with new roles
            $this->em->refresh($this->getUser());
            $tokenStorage->setToken(new PostAuthenticationToken($this->getUser(), 'main', $this->getUser()->getRoles()));

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
            $this->em->getConnection()->executeQuery("DROP TABLE IF EXISTS $tableName;");
            $this->em->getConnection()->executeQuery("
                CREATE TABLE $tableName (
                    last_name VARCHAR(64) NOT NULL,
                    first_name VARCHAR(64) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    company_internal_id VARCHAR(255),
                    roles TEXT NOT NULL,
                    job VARCHAR(128),
                    team VARCHAR(128),
                    PRIMARY KEY (email)
            );");
            $baseSql = "INSERT INTO $tableName ($baseColumns) VALUES ";
            $params = [];
            $i = 0;

            $conv = array_flip(array_keys($this->importColumns));
            while (($d = fgetcsv($handle)) !== false) {
                if (count($d) < 3) {
                    continue; // Skip invalid rows
                }
                
                $params = array_merge($params,[
                    $d[$conv['last_name']] ?? null,
                    $d[$conv['first_name']] ?? null,
                    $d[$conv['email']] ?? null,
                    ($d[$conv['roles']]??false) ? str_replace([' ','+'], ['',','],$d[$conv['roles']]) : 'ROLE_USER',
                    $d[$conv['company_internal_id']] ?? null,
                    $d[$conv['job']] ?? null,
                    $d[$conv['team']] ?? null,
                ]);
                $i += 1;
                if ($i > 1000) {
                    $selector = implode(",",array_fill(0, count($conv), '?'));
                    $selectors = implode(",",array_fill(0, $i, "($selector)"));
                    $this->em->getConnection()->executeQuery($baseSql . $selectors, $params);
                    $params = [];
                    $i = 0;
                }
            }
            $selector = implode(",",array_fill(0, count($conv), '?'));
            $selectors = implode(",",array_fill(0, $i, "($selector)"));
            $this->em->getConnection()->executeQuery($baseSql . $selectors, $params);     
            fclose($handle);

            return $this->render('configuration/companyUsers/csv.html.twig', [
                'total' => $this->em->getConnection()->fetchOne("SELECT COUNT(*) FROM $tableName"),   
                'first100' => !$confirmImport ? $this->em->getConnection()->fetchAllAssociative("SELECT * FROM $tableName LIMIT 100") : null,                        
            ]);
        }
    }


}
