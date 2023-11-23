<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MessageController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/message', name: 'app_message')]
    public function index(): Response
    {
        $room = $this->entityManager->getRoom->findAll();
        $message = $room->getMessages();


        return $this->render('message/index.html.twig', [
            'room' => $room,
            'message' => $message,
        ]);
    }

    #[Route('/message/{id}', name: 'app_message_show')]
    public function show(): Response
    {
        return $this->render('message/show.html.twig', []);
    }

    public function publish(HubInterface $hub): Response
    {
        $update = new Update(
            'https://example.com/books/1',
            json_encode(['status' => 'OutOfStock'])
        );

        $hub->publish($update);

        return new Response('published!');
    }
}
// Ceci est un commentaire PHP