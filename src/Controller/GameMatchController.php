<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameMatchController extends AbstractController
{
    #[Route('/game/match', name: 'app_game_match')]
    public function index(): Response
    {
        return $this->render('game_match/index.html.twig', [
            'controller_name' => 'GameMatchController',
        ]);
    }
}
