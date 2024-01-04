<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Room;
use App\Entity\Utilisateur;
use App\Repository\MessageRepository;
use App\Repository\RoomRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\WebLink\Link;

class ChatController extends AbstractController
{
    public function __construct(private MessageRepository $messageRepo, private RoomRepository $roomRepo, private EntityManagerInterface $em)
    {
    }

    #[Route('/chat', name: 'app_chat')]
    public function index(Request $request): Response
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
            
            $room->setLastMessage($text);
            
            $this->em->persist($message);
            $this->em->flush();

            return $this->json($message, 200, [], ['groups' => 'message']);
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 500);
        }
    }

    #[Route('/chat/new-room/{userId}', name: 'chat_new_room')]
    public function newRoom(int $userId, UtilisateurRepository $utilisateurRepo): Response
    {
        try {
            /** @var Utilisateur $user */
            $me = $this->getUser();
            $him = $utilisateurRepo->find($userId);
            $room = new Room();
            $room->setLastMessage('');
            $room->addUtilisateur($me);
            $room->addUtilisateur($him);

            $this->em->persist($room);
            $this->em->flush();

            $rooms = $this->roomRepo->findRoomsByUser($me);

            return $this->render('chat/index.html.twig', [
                'rooms' => $rooms,
                'selectedRoomId' => $room->getId(),
            ]);
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 500);
        }
    }
}
