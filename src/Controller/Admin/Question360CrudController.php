<?php

namespace App\Controller\Admin;

use App\Entity\Feedback360\Question360;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class Question360CrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Question360::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ...parent::configureFields($pageName),
            AssociationField::new('thematique')
                ->setRequired(false)
                ->setFormTypeOption('by_reference', true)
        ];
    }
}
