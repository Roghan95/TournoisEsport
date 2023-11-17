<?php

namespace App\Controller;

use App\Entity\Jeu;
use App\Entity\ParticipantTournoi;
use App\Entity\PseudoEnJeu;
use App\Entity\Tournoi;
use App\Form\TournoiType;
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
    public function joinTournoi(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $tournoiId = $data['tournoiId'];
            // return $this->json(['success' => true, 'tournoiId' => $data], 200);
            $tournoi = $this->tournoiRepo->find($tournoiId);

            $participantTournoi = new ParticipantTournoi();
            $participantTournoi->setTournoi($tournoi);
            $participantTournoi->setUtilisateur($this->getUser());
            $participantTournoi->setInGamePseudo('Bays');

            // $tournoi->addParticipantTournoi($participantTournoi);

            $this->em->persist($participantTournoi);
            $this->em->flush();

            return $this->json(['success' => true], 200);
        } catch (\Throwable $th) {
            // Gérer les erreurs éventuelles
            return new JsonResponse(['success' => false, 'error' => $th->getMessage()]);
        }
    }

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

    #[Route('/save-new-pseudo', name: 'save_new_pseudo', methods: ['POST'])]
    public function savePseudo(Request $request, JeuRepository $jeuRepo): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $pseudo = $data['pseudo'];
            $jeuId = $data['jeuId'];

            $jeu = $jeuRepo->find($jeuId);

            $pseudoEnJeu = new PseudoEnJeu();
            $pseudoEnJeu->setPseudo($pseudo);
            $pseudoEnJeu->setUtilisateur($this->getUser());
            $pseudoEnJeu->setJeu($jeu);

            $this->em->persist($pseudoEnJeu);
            $this->em->flush();

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
}
