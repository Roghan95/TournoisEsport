<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Form\EquipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EquipeController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/equipe', name: 'app_equipe')]
    public function index(): Response
    {

        return $this->render('equipe/index.html.twig', [
            'controller_name' => 'EquipeController',
        ]);
    }

    #[Route('/equipe/new', name: 'new_equipe')]
    public function ajouter(Request $request, Security $security): Response
    {
        // Vérifie si l'utilisateur est connecté
        $user = $security->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour créer une équipe');
            return $this->redirectToRoute('app_login');
        }

        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $equipe->setProprietaire($user);

            $this->em->persist($equipe);
            $this->em->flush();
            return $this->redirectToRoute('app_mon_profil');
        }
        return $this->render('equipe/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/equipe/{id}', name: 'app_equipe_detail')]
    public function detail($id): Response
    {
        return $this->render('equipe/detail.html.twig', [
            'controller_name' => 'EquipeController',
            'id' => $id
        ]);
    }
}
