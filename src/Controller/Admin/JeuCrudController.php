<?php

namespace App\Controller\Admin;

use App\Entity\Jeu;
use App\Field\VichImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

#[IsGranted('ROLE_ADMIN', statusCode: 403, exceptionCode: 10010)]
class JeuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Jeu::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nomJeu'),
            VichImageField::new('logoFile')->onlyOnForms(),
            ImageField::new('logo')->setBasePath('/uploads/jeux')->hideOnForm(),
        ];
    }
}
