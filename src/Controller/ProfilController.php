<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Follow;
use App\Entity\Utilisateur;
use App\Entity\Notification;
use App\Repository\JeuRepository;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    public function __construct(private UtilisateurRepository $utilisateurRepo, private EntityManagerInterface $em, private EquipeRepository $equipeRepo)
    {
    }

    #[Route('/profil', name: 'app_mon_profil')]
    public function index(EquipeRepository $equipeRepo, JeuRepository $jeuRepo): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        // Check if user is logged in
        if (!$user) {
        return $this->redirectToRoute('app_login');
        }

        $equipes = $equipeRepo->findBy(['proprietaire' => $user]);

        $tournois = $user->getMesTournois();

        $jeux = $jeuRepo->findAll();
        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'equipes' => $equipes,
            'tournois' => $tournois,
            'jeux' => $jeux,
        ]);
    }

    // Fonction qui permet d'afficher les paramètres du profil
    #[Route('/profil/param', name: 'app_mon_profil_param')]
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

        return $this->redirectToRoute('app_mon_profil');
    }

    // Fonction qui permet d'afficher le profil d'un utilisateur avec ces informations
    #[Route('/profil/{id}', name: 'app_user_profil')]
    public function userProfil(Utilisateur $user, EquipeRepository $equipeRepo): Response
    {
    // Check if the user exists
    if (!$user) {
        throw $this->createNotFoundException('L\'utilisateur demandé n\'existe pas.');
    }

    $equipes = $equipeRepo->findBy(['proprietaire' => $user]);

    // Check if the teams exist
    if (!$equipes) {
        $this->addFlash('error', 'Aucune équipe trouvée pour cet utilisateur.');
    }

    $tournois = $user->getMesTournois();

    // Check if the tournaments exist
    if (!$tournois) {
        $this->addFlash('error', 'Aucun tournoi trouvé pour cet utilisateur.');
    }

    $alreadyFollow = false;

    // Check if user is logged in before trying to access User object
    if ($this->getUser()) {
        /** @var Utilisateur $me */
        $me = $this->getUser();

        // verify if getFollows contains $user
        $follows = $me->getFollows();
        foreach ($follows as $key => $follow) {
            if ($follow->getFollowing()->getId() == $user->getId()) {
                $alreadyFollow = true;
                // stop loop
                break;
            }
        }
    }

    return $this->render('profil/index.html.twig', [
        'user' => $user,
        'equipes' => $equipes,
        'tournois' => $tournois,
        'alreadyFollow' => $alreadyFollow
    ]);
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

        return $this->redirectToRoute('app_mon_profil');
    }

    // Fonction qui permet d'inviter un utilisateur dans une équipe
    #[Route('/api/invite-user', name: 'api_invite_user', methods: ['POST'])]
    public function inviteUser(Request $request): Response
    {
        try {
            /** @var Utilisateur $user */
            $me = $this->getUser();

            $data = json_decode($request->getContent(), true);
            $userId = $data['userId'];

            /** @var Utilisateur $user */
            $him = $this->utilisateurRepo->find($userId);

            $notification = new Notification();
            $notification->setTexte("L'utilisateur " . $me->getPseudo() . " vous a invité dans son équipe");
            $notification->setType("invitationForTeam");
            $notification->setExpediteur($me);
            $notification->setDestinataire($him);
            $notification->setEquipe($me->getEquipes()[0]);
            
            $this->em->persist($notification);
            $this->em->flush();

            return $this->json([
                'code' => 200,
                'success' => true,
                'message' => 'Invitation envoyée'
            ], 200);
        } catch (\Throwable $th) {
            return $this->json([
                'code' => 500,
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    //Fonction qui permet de rejoindre une équipe
    #[Route('/equipe/rejoindre/{id}', name: 'equipe_rejoindre')]
    public function joinTeam(Equipe $equipe): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $equipe->addMembre($user);

        $this->em->flush();

        return $this->redirectToRoute('app_mon_profil');
    }

    // Fonction qui permet de follow un utilisateur
    #[Route('/api/follow-user', name: 'api_follow_user', methods: ['POST'])]
    public function followUser(Request $request): Response
    {
        try {
            /** @var Utilisateur $user */
            $me = $this->getUser();

            $data = json_decode($request->getContent(), true);
            $userId = $data['userId'];

            /** @var Utilisateur $user */
            $him = $this->utilisateurRepo->find($userId);

        
            $alreadyFollow = false; // if already follow default false
            $follows = $me->getFollows(); // get all follows
            // verify if getFollows contains $user
            foreach ($follows as $key => $follow) {
                // if already follow, remove follow
                if ($follow->getFollowing()->getId() == $him->getId()) {
                    $this->em->remove($follow);
                    $this->em->flush();
                    $alreadyFollow = true; // if already follow true 
                    // stop loop
                    break;
                }
            }

            // if already follow, remove follow
            if($alreadyFollow){
                return $this->json([
                    'code' => 200,
                    'success' => true,
                    'message' => 'Vous ne suivez plus cet utilisateur',
                    'textContent' => 'Follow'
                ], 200);
            }
    

            $follow = new Follow();
            $follow->setFollower($me);
            $follow->setFollowing($him);

            $this->em->persist($follow);

            $me->addFollow($follow);

            $this->em->flush();

            return $this->json([
                'code' => 200,
                'success' => true,
                'message' => 'Vous suivez cet utilisateur',
                'textContent' => 'Unfollow'
            ], 200);
        } catch (\Throwable $th) {
            return $this->json([
                'code' => 500,
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
