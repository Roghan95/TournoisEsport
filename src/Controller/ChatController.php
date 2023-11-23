<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Utilisateur;
use App\Repository\MessageRepository;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function index(RoomRepository $roomRepo): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $rooms = $roomRepo->findRoomsByUser($user);
        return $this->render('chat/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    #[Route('/chat/room/{id}', name: 'chat_room', methods: ['GET'])]
    public function room(Room $room, MessageRepository $messageRepo): Response
    {
        try {
            $messages = $messageRepo->findMessagesByRoom($room);

            return $this->json($messages, 200, [], ['groups' => 'message']);
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 500);
        }
    }

    #[Route('/chat/new-message', name: 'chat_new_message', methods: ['POST'])]
    public function newMessage(Request $request, MessageRepository $messageRepo): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $roomId = $messageRepo->find($data['roomId']);
            $message = $messageRepo->find($data['message']);
            // $messages = $messageRepo->findMessagesByRoom($room);

            // return $this->json($messages, 200, [], ['groups' => 'message']);
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 500);
        }
    }
}
