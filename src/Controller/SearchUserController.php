<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchUserController extends AbstractController
{
    #[Route('/search/user', name: 'app_search_user')]
    public function index(Request $request, UtilisateurRepository $userRepo, EquipeRepository $equipeRepo): Response
    {
        $search = $request->query->get('search');

        // Filtrer le champ de recherche contre les injections XSS
        $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $equipes = $equipeRepo->findBy(['proprietaire' => $this->getUser()]);
        if ($search != null) {
            $users = $userRepo->search($search);
            return $this->render('search_user/index.html.twig', [
                'users' => $users,
                'equipes' => $equipes,
            ]);
        }
        return $this->render('search_user/index.html.twig', [
            'users' => null,
            'equipes' => $equipes,
        ]);
    }
}
