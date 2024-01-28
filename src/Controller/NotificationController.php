<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    public function index(EntityManager $entityManager, UtilisateurRepository $userRepo): Response
    {
        $user = $this->getUser();

        return $this->render('notification/index.html.twig', [
            'user' => $user,
        ]);
    }
}
