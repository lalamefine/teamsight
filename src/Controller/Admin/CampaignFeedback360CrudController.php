<?php

namespace App\Controller\Admin;

use App\Entity\Feedback360\CampaignFeedback360;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CampaignFeedback360CrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CampaignFeedback360::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
