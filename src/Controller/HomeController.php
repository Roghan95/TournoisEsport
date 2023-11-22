<?php

namespace App\Controller;

use App\Repository\JeuRepository;
use App\Repository\TournoiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, SessionInterface $session, TournoiRepository $tournoiRepository, JeuRepository $jeuRepository): Response
    {
        /***
         * @var \App\Entity\Utilisateur $user
         */
        $user = $this->getUser();
        if ($user != null && !$user->isVerified()) {
            return $this->render('home/check_mail.html.twig');
        }

        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            $jeuId = $data['jeuId'];
            $session->set('jeu_selectionne', $jeuId);
        }

        $jeuId = $session->get('jeu_selectionne', null);

        if ($jeuId == null || $jeuId == '') {
            $tournois = $tournoiRepository->findAll();
        } else {
            $tournois = $tournoiRepository->findBy(['jeu' => $jeuId]);
        }

        $jeux = $jeuRepository->findAll();

        return $this->render('home/index.html.twig', [
            'tournois' => $tournois,
            'jeux' => $jeux,
            'jeuSelectionne' => $jeuId,
        ]);
    }
}
