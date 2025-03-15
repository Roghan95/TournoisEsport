<?php

namespace App\Controller;

use App\Repository\JeuRepository;
use App\Repository\TournoiRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, SessionInterface $session, TournoiRepository $tournoiRepository, JeuRepository $jeuRepository, PaginatorInterface $paginator): Response
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
            $data = $tournoiRepository->findAll();
            $tournois = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                6
            );
            // Si l'id du jeu n'est pas null ou vide, on récupère les tournois du jeu
        } else {
            $data = $tournoiRepository->findBy(['jeu' => $jeuId]);
            $tournois = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                6
            );
        }

        // On récupère tous les jeux
        $jeux = $jeuRepository->findAll();
        // On initialise la variable $selectedJeu à null
        $selectedJeu = null;

        if ($jeuId != null) {
            // Parcourt chaque jeu dans le tableau $jeux
            foreach ($jeux as $key => $jeu) {
                // Si l'ID du jeu actuel correspond à $jeuId
                if ($jeu->getId() == $jeuId) {
                    // Le jeu actuel est le jeu sélectionné, donc on le stocke dans $selectedJeu
                    $selectedJeu = $jeu;

                    // On retire le jeu sélectionné du tableau $jeux pour éviter de le traiter à nouveau
                    unset($jeux[$key]);

                    // On arrête la boucle car on a trouvé le jeu recherché
                    break;
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'tournois' => $tournois,
            'jeux' => $jeux,
            'selectedJeu' => $selectedJeu,
        ]);
    }
}
