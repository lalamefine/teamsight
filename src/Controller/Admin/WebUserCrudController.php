<?php

namespace App\Controller\Admin;

use App\Entity\WebUser;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class WebUserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WebUser::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            ...parent::configureFields($pageName),
            AssociationField::new('company')
                ->setRequired(false)
                ->setFormTypeOption('by_reference', true),
        ];
    }
}
