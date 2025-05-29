<?php

namespace App\Controller\Admin;

use App\Entity\Feedback360\Template360;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class Template360CrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Template360::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ...parent::configureFields($pageName),
            ArrayField::new('responses')
                ->setRequired(true),
            AssociationField::new('company')
                ->setRequired(false)
                ->setFormTypeOption('by_reference', true),
        ];
    }
}
