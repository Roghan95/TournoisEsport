<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Repository\EquipeRepository;
use App\Repository\JeuRepository;
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
    public function index(EquipeRepository $equipeRepo, JeuRepository $jeuRepo): Response
    {
        $equipes = $equipeRepo->findBy(['proprietaire' => $this->getUser()]);
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $tournois = $user->getMesTournois();
        $jeux = $jeuRepo->findAll();
        return $this->render('profil/index.html.twig', [
            'user' => $this->getUser(),
            'equipes' => $equipes,
            'tournois' => $tournois,
            'jeux' => $jeux,
        ]);
    }

    #[Route('/profil/param', name: 'app_profil_param')]
    public function profilParam(): Response
    {
        return $this->render('profil/param_acc.html.twig', []);
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

    // Fonction pour qui permet de supprimer une équipe
    #[Route('/equipe/supprimer/{id}', name: 'equipe_supprimer')]
    public function deleteTeam(Equipe $equipe): Response
    {
        // Supprimer les participants de l'équipe
        foreach ($equipe->getMembres() as $membre) {
            $equipe->removeMembre($membre);
        }

        $this->em->remove($equipe);
        $this->em->flush();

        return $this->redirectToRoute('app_profil');
    }
}
