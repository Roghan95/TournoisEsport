<?php

namespace App\Controller;

use App\Entity\GameMatch;
use App\Entity\Tournoi;
use App\Form\GameMatchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameMatchController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    #[Route('/game/match', name: 'app_game_match')]
    public function index(): Response
    {
        return $this->render('game_match/index.html.twig', [
            'controller_name' => 'GameMatchController',
        ]);
    }

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

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournoi/new.html.twig', [
            // 'match' => $match,
            'form' => $form,
        ]);
    }
}
