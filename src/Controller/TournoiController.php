<?php

namespace App\Controller;

use App\Entity\ParticipantTournoi;
use App\Entity\PseudoEnJeu;
use App\Entity\Tournoi;
use App\Entity\Utilisateur;
use App\Form\TournoiType;
use App\Repository\EquipeRepository;
use App\Repository\JeuRepository;
use App\Repository\PseudoEnJeuRepository;
use App\Repository\TournoiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/tournoi')]
class TournoiController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private TournoiRepository $tournoiRepo
    ) {
    }
    // #[Route('/', name: 'app_tournoi_index', methods: ['GET'])]
    // public function index(TournoiRepository $tournoiRepository): Response
    // {
    //     return $this->render('tournoi/index.html.twig', [
    //         'tournois' => $tournoiRepository->findAll(),
    //     ]);
    // }

    // Fonction permettant de crée un tournoi
    #[Route('/new', name: 'app_tournoi_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tournoi = new Tournoi();
        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($tournoi->getDateDebut() > $tournoi->getDateFin()) {
                $this->addFlash('error', 'La date de début doit être antérieure à la date de fin');
                return $this->redirectToRoute('app_tournoi_new');
            }
            $description = $request->request->get('tournoi-description');

            $tournoi->setDescription($description);
            $tournoi->setOrganisateur($this->getUser());

            $this->em->persist($tournoi);
            $this->em->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

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
            $pseudoEnJeu = $pseudoEnJeuRepo->findOneBy(['utilisateur' => $user, 'jeu' => $tournoi->getJeu()->getId()]);
            $participantTournoi = new ParticipantTournoi();
            $participantTournoi->setTournoi($tournoi);
            $participantTournoi->setUtilisateur($user);
            $participantTournoi->setInGamePseudo($pseudoEnJeu->getPseudo());

            $tournoi->addParticipantTournoi($participantTournoi);
            $equipe = $equipeRepo->findOneBy(['proprietaire' => $user, 'jeu' => $tournoi->getJeu()]);
            if ($equipe !== null) {
                $participantTournoi->setEquipe($equipe);
            }

            $this->em->persist($participantTournoi);
            $this->em->flush();

            return $this->json(['success' => true, 'pseudo' => $pseudoEnJeu->getPseudo(),], 200);
        } catch (\Throwable $th) {
            // Gérer les erreurs éventuelles
            return new JsonResponse(['success' => false, 'error' => $th->getMessage()], 400);
        }
    }

    // Fonction permettant de vérifier si un utilisateur est déjà inscrit à un tournoi
    #[Route('/check-pseudo/{jeuId}', name: 'check_pseudo', methods: ['GET'])]
    public function checkPseudo($jeuId, PseudoEnJeuRepository $pseudoEnJeuRepo): Response
    {
        try {
            $pseudoEnJeu = $pseudoEnJeuRepo->findOneBy(['utilisateur' => $this->getUser(), 'jeu' => $jeuId]);

            if ($pseudoEnJeu === null) {
                return $this->json(['success' => false, 'pseudo' => null], 200);
            }

            return $this->json(['success' => true, 'pseudo' => $pseudoEnJeu->getPseudo()], 200);
        } catch (\Throwable $th) {
            // Gérer les erreurs éventuelles
            return new JsonResponse(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    // Fonction permettant de sauvegarder un nouveau pseudo
    #[Route('/save-new-pseudo', name: 'save_new_pseudo', methods: ['POST'])]
    public function savePseudo(Request $request, JeuRepository $jeuRepo): Response
    {
        // Vérifier que le token est valide
        // if (!$this->isCsrfTokenValid('save_new_pseudo', $request->request->get('_token'))) {
        //     return $this->json(['success' => false, 'error' => 'Token invalide'], 400);
        // }

        try {
            // Récupérer les données envoyées et les décoder
            $data = json_decode($request->getContent(), true);

            // Récupérer les données
            $pseudo = $data['pseudo'];
            $jeuId = $data['jeuId'];

            // Récupérer l'id du jeu
            $jeu = $jeuRepo->find($jeuId);

            // Créer un nouveau pseudo
            $pseudoEnJeu = new PseudoEnJeu();
            $pseudoEnJeu->setPseudo($pseudo);
            $pseudoEnJeu->setUtilisateur($this->getUser());
            $pseudoEnJeu->setJeu($jeu);

            // Sauvegarder le pseudo
            $this->em->persist($pseudoEnJeu);
            $this->em->flush();

            // Retourner une réponse en JSON
            return $this->json(['success' => true], 200);
        } catch (\Throwable $th) {
            // Gérer les erreurs éventuelles
            return new JsonResponse(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    // Fonction qui permet d'afficher le détail d'un tournoi
    #[Route('/{id}', name: 'app_tournoi_show', methods: ['GET'])]
    public function show(Tournoi $tournoi): Response
    {
        $id = $tournoi->getId();
        $tournoi = $this->em->getRepository(Tournoi::class)->find($id);
        return $this->render('tournoi/show.html.twig', [
            'tournoi' => $tournoi,
        ]);
    }

    // Fonction permettant de modifier un tournoi
    #[Route('/{id}/edit', name: 'app_tournoi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournoi/edit.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
        ]);
    }

    // Fonction permettant de supprimer un tournoi
    #[Route('/{id}', name: 'app_tournoi_delete', methods: ['POST'])]
    public function delete(Request $request, Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tournoi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tournoi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    // Fonction permettant de supprimer un participant d'un tournoi
    #[Route('/{id}/delete-participant', name: 'app_tournoi_delete_participant', methods: ['POST'])]
    public function deleteParticipant(Request $request, ParticipantTournoi $participantTournoi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $participantTournoi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($participantTournoi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    // Fonction permettant au participant de rejoindre un tounoi 
    #[Route('/{id}/join', name: 'app_tournoi_join', methods: ['POST'])]
    public function join(Request $request, Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('join' . $tournoi->getId(), $request->request->get('_token'))) {
            $participantTournoi = new ParticipantTournoi();
            $participantTournoi->setTournoi($tournoi);
            $participantTournoi->setUtilisateur($this->getUser());
            $entityManager->persist($participantTournoi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }


    // Fonction permettant au joueur de quitter un tournoi
    #[Route('/{id}/leave', name: 'app_tournoi_leave', methods: ['POST'])]
    public function leave(Request $request, Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('leave' . $tournoi->getId(), $request->request->get('_token'))) {
            $participantTournoi = $this->em->getRepository(ParticipantTournoi::class)->findOneBy(['tournoi' => $tournoi, 'utilisateur' => $this->getUser()]);
            $entityManager->remove($participantTournoi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
