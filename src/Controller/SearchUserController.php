<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchUserController extends AbstractController
{
    #[Route('/search/user', name: 'app_search_user')]
    public function index(Request $request, UtilisateurRepository $userRepo): Response
    {
        $search = $request->query->get('search');
        if ($search != null) {
            $users = $userRepo->search($search);
            return $this->render('search_user/index.html.twig', [
                'users' => $users,
            ]);
        }
        return $this->render('search_user/index.html.twig', [
            'users' => null,
        ]);
    }
}
