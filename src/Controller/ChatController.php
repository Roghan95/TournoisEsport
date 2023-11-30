<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Room;
use App\Entity\Utilisateur;
use App\Repository\MessageRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    public function __construct(private MessageRepository $messageRepo, private RoomRepository $roomRepo, private EntityManagerInterface $em)
    {
    }

    #[Route('/chat', name: 'app_chat')]
    public function index(): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $rooms = $this->roomRepo->findRoomsByUser($user);
        return $this->render('chat/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    #[Route('/chat/room/{id}', name: 'chat_room', methods: ['GET'])]
    public function room(Room $room): Response
    {
        try {
            $messages = $this->messageRepo->findMessagesByRoom($room);

            return $this->json($messages, 200, [], ['groups' => 'message']);
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 500);
        }
    }

    #[Route('/chat/new-message', name: 'chat_new_message', methods: ['POST'])]
    public function newMessage(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $roomId = $data['roomId'];
            $text = $data['message'];

            $room = $this->roomRepo->findOneBy(['id' => $roomId]);

            $utilisateurs = $room->getUtilisateurs()->toArray();

            /** @var Utilisateur $currentUser */
            $currentUser = $this->getUser();

            foreach ($utilisateurs as $utilisateur) {
                if ($utilisateur->getId() !== $currentUser->getId()) {
                    $destinataire = $utilisateur;
                } else {
                    $expediteur = $utilisateur;
                }
            }

            $message = new Message();
            $message->setRoom($room);
            $message->setTexteMessage($text);
            $message->setExpediteur($expediteur);
            $message->setDestinataire($destinataire);

            $this->em->persist($message);
            $this->em->flush();

            return $this->json($message, 200, [], ['groups' => 'message']);
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 500);
        }
    }
}
