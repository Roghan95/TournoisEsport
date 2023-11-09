<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    public function __construct(private UtilisateurRepository $utilisateurRepo)
    {
    }

    #[Route('/profil', name: 'app_profil')]
    public function index(EquipeRepository $equipeRepo): Response
    {
        /** @var Equipe $equipe */
        $equipe = $equipeRepo->findOneBy(['proprietaire' => $this->getUser()]);

        return $this->render('profil/index.html.twig', [
            'user' => $this->getUser(),
            'equipe' => $equipe
        ]);
    }
}
