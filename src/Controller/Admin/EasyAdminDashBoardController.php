<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\CompanyConfig;
use App\Entity\ObsProfile;
use App\Entity\WebUser;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class EasyAdminDashBoardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        return $this->redirectToRoute('admin_web_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Teamsight');
    }

    public function configureMenuItems(): iterable
    {
        // yield MenuItem::dashboard('Dashboard', 'fa fa-home');
        
        // Structure des entités par namespace/groupe logique
        $entityGroups = [];
        
        $entityDir = $this->getParameter('kernel.project_dir') . '/src/Controller/Admin';
        foreach (scandir($entityDir) as $file) {
            if (preg_match('/^([A-Za-z0-9_]+)\.php$/', $file, $matches)) {
                $AdminControllerClassName = 'App\\Controller\\Admin\\' . $matches[1];
                $reflexionClass = new \ReflectionClass($AdminControllerClassName);
                if($reflexionClass->hasMethod('getEntityFqcn')) {
                    $method = $reflexionClass->getMethod('getEntityFqcn');
                    if($method->isStatic() && $method->isPublic()) {
                        $className = $method->invoke(null);
                        if (class_exists($className)) {
                            // Obtenir le nom court de la classe
                            $entityParts = explode('\\', $className);
                            $entityName = array_pop($entityParts);
                            
                            // Déterminer le groupe (peut être basé sur le namespace ou une logique métier)
                            $group = $this->determineEntityGroup($className, $entityName);
                            
                            // Ajouter l'entité au groupe approprié
                            if (!isset($entityGroups[$group])) {
                                $entityGroups[$group] = [];
                            }
                            $entityGroups[$group][] = [
                                'name' => $entityName,
                                'class' => $className
                            ];
                        }
                    }
                }
            }
        }
        
        // Générer les sections et sous-menus basés sur les groupes
        foreach ($entityGroups as $groupName => $entities) {
            if(count($entities) === 1) {
                $entity = $entities[0];
                yield MenuItem::linkToCrud($entity['name'], 'fas fa-database', $entity['class']);
                continue;
            }else{
                $subMenu = MenuItem::subMenu($groupName, 'fas fa-folder');
                $items = [];
                foreach ($entities as $entity) {
                    $items[] = MenuItem::linkToCrud($entity['name'], 'fas fa-database', $entity['class']);
                }
                $subMenu->setSubItems($items);
                yield $subMenu;

            }
        }
    }

    /**
     * Détermine le groupe logique d'une entité
     */
    private function determineEntityGroup(string $className, string $entityName): string
    {
        $parts = explode('\\', $className);
        if (count($parts) >= 3) {
            $moduleNamespace = $parts[2] ?? ''; // Par exemple, le 3ème segment du namespace
            if (!empty($moduleNamespace)) {
                return $moduleNamespace;
            }
        }
        // Groupe par défaut
        return 'Autres';
    }
}
