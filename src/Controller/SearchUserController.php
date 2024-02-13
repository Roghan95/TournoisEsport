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
        // Récupère la valeur de la requête 'search' à partir de l'URL
        $search = $request->query->get('search');

        // Filtrer le champ de recherche contre les injections XSS
        $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $equipes = $equipeRepo->findBy(['proprietaire' => $this->getUser()]);
    
        // Si 'search' n'est pas null, c'est-à-dire si l'utilisateur a entré quelque chose dans le champ de recherche
        if ($search != null) {
            // Recherche les utilisateurs correspondant à la valeur de 'search'
            $users = $userRepo->search($search);
    
            // Rend la vue 'search_user/index.html.twig' avec les utilisateurs et les équipes trouvés
            return $this->render('search_user/index.html.twig', [
                'users' => $users,
                'equipes' => $equipes,
            ]);
        }
    
        // Si 'search' est null, c'est-à-dire si l'utilisateur n'a rien entré dans le champ de recherche
        // Rend la vue 'search_user/index.html.twig' sans utilisateurs mais avec les équipes trouvées
        return $this->render('search_user/index.html.twig', [
            'users' => null,
            'equipes' => $equipes,
        ]);
    }
}
