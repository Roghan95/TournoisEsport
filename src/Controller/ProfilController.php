<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Repository\EquipeRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    public function __construct(private UtilisateurRepository $utilisateurRepo, private EntityManagerInterface $em)
    {
    }

    #[Route('/profil', name: 'app_profil')]
    public function index(EquipeRepository $equipeRepo): Response
    {
        $equipes = $equipeRepo->findBy(['proprietaire' => $this->getUser()]);
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $tournois = $user->getMesTournois();
        return $this->render('profil/index.html.twig', [
            'user' => $this->getUser(),
            'equipes' => $equipes,
            'tournois' => $tournois
        ]);
    }

    // Fonction pour qui permet de quitter une équipe
    #[Route('/equipe/quitter/{id}', name: 'equipe_quitter')]
    public function leaveTeam(Equipe $equipe): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $user->removeEquipe($equipe);

        $this->em->flush();

        return $this->redirectToRoute('app_profil');
    }
}
