<?php

namespace App\Controller;

use App\Entity\Tournoi;
use App\Entity\GameMatch;
use App\Entity\Participant;
use App\Form\GameMatchType;
use App\Repository\EquipeRepository;
use App\Repository\TournoiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameMatchController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    // #[Route('/game/match', name: 'app_game_match')]
    // public function index(): Response
    // {
    //     return $this->render('game_match/index.html.twig', [
    //         'controller_name' => 'GameMatchController',
    //     ]);
    // }

    #[Route('/game/match/new/{id}', name: 'new_match')]
    public function new(Request $request, Tournoi $tournoi): Response
    {
        $match = new GameMatch();
        $form = $this->createForm(GameMatchType::class, $match);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $match->setTournoi($tournoi);
            $match->setStatut('pending');
            $this->em->persist($match);
            $this->em->flush();

            return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game_match/new.html.twig', [
            'match' => $match,
            'form' => $form,
        ]);
    }

    // Fonction permettant de rejoindre un match en tant que joueur ou équipe (selon le type de tournoi)
    #[Route('/delete/match/{id}', name: 'delete_match')]
    public function delete(GameMatch $match, Request $request, TournoiRepository $tournoiRepo): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        //  Vérification si l'utilisateur est bien le propriétaire du match
        // $token = new CsrfToken('delete_match', $request->request->get('_csrf_token'));
        // if (!$this->isCsrfTokenValid('delete_match', $token)) {
        //     throw $this->createAccessDeniedException('Token CSRF invalide');
        // }
        $tournoi = $tournoiRepo->findOneBy(['id' => $match->getTournoi()->getId()]);
        if ($user != $tournoi->getOrganisateur()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas le propriétaire de ce match');
        }
        // Vérification du token CSRF

        $this->em->remove($match);
        $this->em->flush();

        return $this->redirectToRoute('app_tournoi_show', ['id' => $match->getTournoi()->getId()], Response::HTTP_SEE_OTHER);
    }

    // // Fonction permettant de rejoindre un match en tant que joueur ou équipe (selon le type de tournoi)
    #[Route('/game/match/join/{id}', name: 'join_match')]
    public function join(Request $request, GameMatch $match, EquipeRepository $equipeRepo): Response
    {
        // Vérification du token CSRF
        $token = new CsrfToken('join_match', $request->request->get('_csrf_token'));
        if (!$this->isCsrfTokenValid('join_match', $token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        /** @var Utilisateur $user */
        $user = $this->getUser();

        $participant = new Participant();
        $participant->setUtilisateur($user);
        $participant->setGameMatch($match);

        // Vérification si l'utilisateur est déjà inscrit au match
        $participants = $match->getParticipants();
        foreach ($participants as $participant) {
            if ($participant->getUtilisateur() == $user) {
                throw $this->createAccessDeniedException('Vous êtes déjà inscrit à ce match');
            }
        }

        // Vérification si l'utilisateur est déjà inscrit au tournoi
        $participantsTournoi = $match->getTournoi()->getParticipantTournois();
        foreach ($participantsTournoi as $participantTournoi) {
            if ($participantTournoi->getUtilisateur() == $user) {
                throw $this->createAccessDeniedException('Vous êtes déjà inscrit à ce tournoi');
            }
        }

        // // Récupération de l'équipe de l'utilisateur pour le même jeu que le match
        // if ($match->getTypeMatch() != 'solo') {
        //     $equipe = $equipeRepo->findOneBy(['proprietaire' => $user, 'jeu' => $match->getTournoi()->getJeu()]);
        // }

        $participant->setEquipe($equipe);
        $this->em->persist($participant);

        $match->addParticipant($participant);
        $this->em->persist($match);

        $this->em->flush();

        return $this->redirectToRoute('app_tournoi', [], Response::HTTP_SEE_OTHER);
    }
}
