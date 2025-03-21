<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Jeu;
use App\Repository\JeuRepository;

class JeuController extends AbstractController
{
    public function __construct(private JeuRepository $jeuRepository) {}

    #[Route('/jeu', name: 'app_jeu')]
    public function index(): Response
    {
        $jeu = $this->jeuRepository->findAll(orderBy: ['nomJeu' => 'ASC']);

        return $this->render('jeu/index.html.twig', [
            'jeu' => $jeu,

        ]);
    }
}
