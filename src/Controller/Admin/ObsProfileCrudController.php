<?php

namespace App\Controller\Admin;

use App\Entity\Feedback360\ObsProfile;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ObsProfileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ObsProfile::class;
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
