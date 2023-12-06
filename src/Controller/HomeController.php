<?php

namespace App\Controller;

use App\Repository\JeuRepository;
use App\Repository\TournoiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Publisher;

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
            // On récupère les données du formulaire et décoder le json
            $data = json_decode($request->getContent(), true);
            $jeuId = $data['jeuId'];
            // On stock l'id du jeu dans la session
            $session->set('jeu_selectionne', $jeuId);
        }

        // On récupère l'id du jeu dans la session
        $jeuId = $session->get('jeu_selectionne', null);

        // Si l'id du jeu est null ou vide, on récupère tous les tournois
        if ($jeuId == null || $jeuId == '') {
            $tournois = $tournoiRepository->findAll();
            // Si l'id du jeu n'est pas null ou vide, on récupère les tournois du jeu
        } else {
            $tournois = $tournoiRepository->findBy(['jeu' => $jeuId]);
        }
        // On récupère tous les jeux
        $jeux = $jeuRepository->findAll();

        return $this->render('home/index.html.twig', [
            'tournois' => $tournois,
            'jeux' => $jeux,
            'jeuSelectionne' => $jeuId,
        ]);
    }

    #[Route('/publish', name: 'publish')]
    public function publish(HubInterface $hub): Response
    {
        $update = new Update(
            'https://example.com/rooms/1',
            json_encode(['status' => 'OutOfStock'])
        );

        $hub->publish($update);

        return new Response('published!');
    }
}
