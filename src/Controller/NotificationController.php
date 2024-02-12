<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private NotificationRepository $notificationRepo)
    {
    }

    #[Route('/notifications', name: 'app_notification')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }
        $notifications = $this->notificationRepo->findBy(['destinataire' => $this->getUser(), 'statut' => 'pending'], ['createdAt' => 'DESC']);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notifications/decline/{id}', name: 'decline_invitation', methods: ['POST'])]
    public function declineInvitation(Notification $notification)
    {
        $this->em->remove($notification);
        $this->em->flush();
        return $this->redirectToRoute('app_notification');
    }

    #[Route('/notifications/accept/{id}', name: 'accept_invitation', methods: ['POST'])]
    public function acceptInvitation(Notification $notification)
    {
        $equipe = $notification->getEquipe();
        $user = $this->getUser();
        $equipe->addMembre($user);
        $notification->setStatut('accepted');

        $this->em->persist($equipe);
        $this->em->flush();

        return $this->redirectToRoute('app_notification');
    }
}
