<?php

namespace App\Controller;

use App\Entity\Tournoi;
use App\Form\TournoiType;
use App\Entity\Utilisateur;
use App\Entity\ParticipantTournoi;
use App\Repository\EquipeRepository;
use App\Repository\TournoiRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PseudoEnJeuRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ParticipantTournoiRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/tournoi')]
class TournoiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TournoiRepository $tournoiRepo,
        private UtilisateurRepository $utilisateurRepo
    ) {
    }
    // Fonction permettant de crée un tournoi
    #[Route('/new', name: 'app_tournoi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Security $security): Response
    {
        // Vérifie si l'utilisateur est connecté
        $user = $security->getUser();
        // Si l'utilisateur n'est pas connecté, affiche un message d'erreur et redirige vers la page de connexion
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour créer un tournoi');
            return $this->redirectToRoute('app_login');
        }

        // Crée une nouvelle instance de Tournoi
        $tournoi = new Tournoi();
        // Crée un formulaire pour le nouveau tournoi
        $form = $this->createForm(TournoiType::class, $tournoi);
        // Gère la requête du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère l'utilisateur connecté
            $user = $this->getUser();
            // Si l'utilisateur n'est pas connecté, affiche un message d'erreur et redirige vers la page de connexion
            if (!$user) {
                $this->addFlash('error', 'Vous devez être connecté pour créer un tournoi');
                return $this->redirectToRoute('app_login');
            }
            // Si la date de début du tournoi est postérieure à la date de fin, affiche un message d'erreur et redirige vers la page de création de tournoi
            if ($tournoi->getDateDebut() > $tournoi->getDateFin()) {
                $this->addFlash('error', 'La date de début doit être antérieure à la date de fin');
                return $this->redirectToRoute('app_tournoi_new');
            }
            // Récupère la description du tournoi à partir de la requête
            $description = $request->request->get('tournoi-description');

            // Définit la description et l'organisateur du tournoi
            $tournoi->setDescription($description);
            $tournoi->setOrganisateur($this->getUser());

            // Persiste le tournoi dans la base de données
            $this->em->persist($tournoi);
            // Enregistre les changements dans la base de données
            $this->em->flush();

            // Affiche un message de succès et redirige vers la page d'accueil
            $this->addFlash('success', 'Le tournoi a bien été créé');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        // Rend la vue du formulaire de création de tournoi
        return $this->render('tournoi/new.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
        ]);
    }

    // Fonction permettant de rejoindre un tournoi
    #[Route('/join-tournoi', name: 'join_tournoi', methods: ['POST'])]
    public function joinTournoi(Request $request, PseudoEnJeuRepository $pseudoEnJeuRepo, EquipeRepository $equipeRepo): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            /** @var Utilisateur $user */
            $user = $this->getUser();

            $tournoiId = $data['tournoiId'];
            // return $this->json(['success' => true, 'tournoiId' => $data], 200);
            $tournoi = $this->tournoiRepo->find($tournoiId);
            // $pseudoEnJeu = $pseudoEnJeuRepo->findOneBy(['utilisateur' => $user, 'jeu' => $tournoi->getJeu()->getId()]);
            $participantTournoi = new ParticipantTournoi();
            $participantTournoi->setTournoi($tournoi);
            $participantTournoi->setUtilisateur($user);
            // $participantTournoi->setInGamePseudo($pseudoEnJeu->getPseudo());

            // On ajoute le participant au tournoi
            $tournoi->addParticipantTournoi($participantTournoi);

            $equipe = $equipeRepo->findOneBy(['proprietaire' => $user, 'jeu' => $tournoi->getJeu()]);
            if ($equipe !== null) {
                $participantTournoi->setEquipe($equipe);
            }

            $this->em->persist($participantTournoi);
            $this->em->persist($tournoi);
            $this->em->flush();


            return $this->json(['success' => true], 200);
        } catch (\Throwable $th) {
            // Gérer les erreurs éventuelles
            return new JsonResponse(['success' => false, 'error' => $th->getMessage()], 400);
        }
    }

    // Fonction qui permet d'afficher le détail d'un tournoi
    #[Route('/{id}', name: 'app_tournoi_show', methods: ['GET'])]
    public function show(Tournoi $tournoi, ParticipantTournoiRepository $participantTournoiRepo): Response
    {
        $id = $tournoi->getId();
        $tournoi = $this->em->getRepository(Tournoi::class)->find($id);

        // check if connected user participates to the tournament
        $user = $this->getUser();
        $isAlreadyParticipate = false;
        if ($user) {
            $isAlreadyParticipate = $participantTournoiRepo->findOneBy(['tournoi' => $tournoi, 'utilisateur' => $user]);
        }

        if ($tournoi === null) {
            throw $this->createNotFoundException('Le tournoi demandé n\'existe pas.');
        }

        $isTournoiByEquipe = $tournoi->getType() === 'equipe' ? true : false;
        

        $participantsTournoi = $participantTournoiRepo->findBy(['tournoi' => $tournoi]);

        // group participants by team
        $participantsByTeam = [];
        
        if ($isTournoiByEquipe) {
            foreach ($participantsTournoi as $participant) {
                $equipe = $participant->getEquipe();
                if ($equipe) {
                    $participantsByTeam[$equipe->getId()]['participants'][] = $participant;
                    $participantsByTeam[$equipe->getId()]['equipe'] = $equipe;
                    $participantsByTeam[$equipe->getId()]['nomEquipe'] = $equipe->getNomEquipe();
                    $participantsByTeam[$equipe->getId()]['logo'] = $equipe->getLogo();
                }
            }
        }
       
        return $this->render('tournoi/show.html.twig', [
            'tournoi' => $tournoi,
            'isAlreadyParticipate' => $isAlreadyParticipate,
            'user' => $user,
            'isTournoiByEquipe' => $isTournoiByEquipe,
            'participantsByTeam' => $participantsByTeam,
        ]);
    }

    // Fonction permettant de modifier un tournoi
    #[Route('/{id}/edit', name: 'app_tournoi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté et est l'organisateur du tournoi ou s'il a le rôle admin
        $user = $this->getUser();
        if (!$user || $user !== $tournoi->getOrganisateur() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas les droits nécessaires pour modifier ce tournoi');
            return $this->redirectToRoute('app_home');
        }
        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();

                $this->addFlash('success', 'Le tournoi a bien été modifié');
                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Le tournoi n\'a pas pu être modifié');
            }
        }

        return $this->render('tournoi/edit.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
        ]);
    }

    // Fonction permettant de supprimer un tournoi
    // #[Route('/{id}', name: 'app_tournoi_delete', methods: ['POST'])]
    // public function delete(Request $request, Tournoi $tournoi): Response
    // {
    //     $user = $this->getUser();
    //     // Vérifie d'abord si l'utilisateur est connecté et est l'organisateur du tournoi ou s'il a le rôle admin
    //     if ($user && $user === $tournoi->getOrganisateur() || $this->isGranted('ROLE_ADMIN')) {
    //         // Ensuite, vérifie le token CSRF
    //         if ($this->isCsrfTokenValid('delete' . $tournoi->getId(), $request->request->get('_token'))) {
    //             $this->em->remove($tournoi);
    //             $this->em->flush();

    //             $this->addFlash('success', 'Le tournoi a bien été supprimé');
    //         } else {
    //             $this->addFlash('error', 'Le tournoi n\'a pas pu être supprimé');
    //         }
    //     } else {
    //         $this->addFlash('error', 'Vous n\'avez pas les droits nécessaires pour supprimer ce tournoi');
    //     }

    //     return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    // }


    // Fonction permettant de supprimer un participant d'un tournoi
    #[Route('/{id}/delete-participant', name: 'app_tournoi_delete_participant', methods: ['POST'])]
    public function deleteParticipant(Request $request, ParticipantTournoi $participantTournoi): Response
    {
        $tournoiId = $participantTournoi->getTournoi()->getId();

        // if ($this->isCsrfTokenValid('delete' . $participantTournoi->getId(), $request->request->get('_token'))) {
            $this->em->remove($participantTournoi);
            $this->em->flush();
        
            $this->addFlash('success', 'Le participant a bien été supprimé');
        // } else {
            // $this->addFlash('error', 'Le participant n\'a pas pu être supprimé');
        // }

        return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoiId], Response::HTTP_SEE_OTHER);
    }


    // Fonction permettant de participer à un tournoi
    #[Route('/{id}/participer', name: 'tournoi_participer', methods: ['POST'])]
    public function participer(Tournoi $tournoi, EquipeRepository $equipeRepo, ParticipantTournoiRepository $participantTournoiRepo)
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        // check if user is already in tournament
        $isAlreadyParticipate = $participantTournoiRepo->findOneBy(['tournoi' => $tournoi, 'utilisateur' => $user]);
        if ($isAlreadyParticipate) {
            $this->addFlash('error', 'Vous participez déjà à ce tournoi');
            return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
        }

        // Vérifier le type du tournoi
        $typeTournoi = $tournoi->getType();

        $equipe = $equipeRepo->findOneBy(['proprietaire' => $user, 'jeu' => $tournoi->getJeu()]);
        if ($typeTournoi === 'equipe') {

            if ($equipe === null) {
                $this->addFlash('error', 'Vous ne possédez pas d\'équipe pour ce jeu');
                return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
            }

            $membres = $equipe->getMembres();
            foreach ($membres as $membre){
                $participantTournoi = new ParticipantTournoi();
                $participantTournoi->setEquipe($equipe);
                $participantTournoi->setTournoi($tournoi);
                $participantTournoi->setUtilisateur($membre);
                $participantTournoi->setInGamePseudo($membre->getPseudo());
                $this->em->persist($participantTournoi);
            }
 
        }

        $participantTournoi = new ParticipantTournoi();
        $participantTournoi->setEquipe($equipe);
        $participantTournoi->setTournoi($tournoi);
        $participantTournoi->setUtilisateur($user);
        $participantTournoi->setInGamePseudo($user->getPseudo());
        $this->em->persist($participantTournoi);
        $this->em->flush();

        $this->addFlash('success', 'Vous participez au tournoi');
        return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
    }

    #[Route('/{id}/quitter', name: 'tournoi_quitter', methods: ['POST'])]
    public function quitter(Tournoi $tournoi, ParticipantTournoiRepository $participantTournoiRepo, EquipeRepository $equipeRepo): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Vous devez être connecté pour quitter un tournoi'], 401);
        }

        // Verifier le type de tournoi
        $typeTournoi = $tournoi->getType();

        $participantTournoi = $participantTournoiRepo->findOneBy(['tournoi' => $tournoi, 'utilisateur' => $user]);

        if (!$participantTournoi) {
            $this->addFlash('error', 'Vous ne participez pas à ce tournoi');
            return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
        }

        // si c'est par équipe
        if ($typeTournoi === 'equipe') {
            // verifier si l'utilisateur est le propriétaire de l'équipe
            $equipe = $equipeRepo->findOneBy(['proprietaire' => $user, 'id' => $participantTournoi->getEquipe()->getId()]);
            if (!$equipe) {
                $this->addFlash('error', 'Vous n\'êtes pas le propriétaire de l\'équipe');
                return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
            }

            // Supprimer chaque membres de l'équipe du tournoi
            foreach ($equipe->getMembres() as $membre) {
                $participantTournoiMembre = $participantTournoiRepo->findOneBy(['tournoi' => $tournoi, 'utilisateur' => $membre]);
                $this->em->remove($participantTournoiMembre);
            }
        } 
        
        $this->em->remove($participantTournoi);
        

        $this->em->flush();

        $this->addFlash('success', 'Vous avez quitté le tournoi');
        return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
    }
}
