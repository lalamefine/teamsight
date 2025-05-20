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
        // Automatically yield MenuItems for all entities in App\Entity namespace
        $entityDir = $this->getParameter('kernel.project_dir') . '/src/Entity';
        foreach (scandir($entityDir) as $file) {
            if (preg_match('/^([A-Za-z0-9_]+)\.php$/', $file, $matches)) {
                $className = 'App\\Entity\\' . $matches[1];
                $AdminControllerClassName = 'App\\Controller\\Admin\\' . $matches[1] . 'CrudController';
                if (class_exists($className) && class_exists($AdminControllerClassName)) {
                    yield MenuItem::linkToCrud($matches[1], 'fas fa-database', $className);
                }
            }
        }
    }
}
