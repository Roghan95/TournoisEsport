<?php

namespace App\Controller;

use App\Entity\ParticipantTournoi;
use App\Entity\Tournoi;
use App\Form\TournoiType;
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
        private EntityManagerInterface $em
    ) {
    }
    // #[Route('/', name: 'app_tournoi_index', methods: ['GET'])]
    // public function index(TournoiRepository $tournoiRepository): Response
    // {
    //     return $this->render('tournoi/index.html.twig', [
    //         'tournois' => $tournoiRepository->findAll(),
    //     ]);
    // }

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

    #[Route('/join-tournoi', name: 'join_tournoi', methods: ['POST'])]
    public function joinTournoi(Request $request, TournoiRepository $tournoiRepo): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $tournoiId = $data['tournoiId'];
            // return $this->json(['success' => true, 'tournoiId' => $data], 200);
            $tournoi = $tournoiRepo->find($tournoiId);

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

    #[Route('/{id}', name: 'app_tournoi_show', methods: ['GET'])]
    public function show(Tournoi $tournoi): Response
    {
        return $this->render('tournoi/show.html.twig', [
            'tournoi' => $tournoi,
        ]);
    }

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
