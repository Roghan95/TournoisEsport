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
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    public function __construct(private MessageRepository $messageRepo, private RoomRepository $roomRepo, private EntityManagerInterface $em) {}

    #[Route('/chat', name: 'app_chat')]
    public function index(Request $request): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accèder à cette page !');
            return $this->redirectToRoute('app_login');
        }

        $rooms = $this->roomRepo->findRoomsByUser($user);

        $roomId = $request->query->get('room');

        return $this->render('chat/index.html.twig', [
            'rooms' => $rooms,
            'roomId' => $roomId
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

    // Fonction permettant de récupérer les messages d'une room et de les afficher dans le chat
    #[Route('/chat/new-message', name: 'chat_new_message', methods: ['POST'])]
    public function newMessage(Request $request): Response
    {
        try {
            // Décodage du contenu JSON de la requête en un tableau associatif
            $data = json_decode($request->getContent(), true);
            // Extraction de l'ID de la salle et du texte du message à partir des données
            $roomId = $data['roomId'];
            $text = $data['message'];

            // Récupération de l'entité de la salle à partir de la base de données en utilisant l'ID de la salle
            $room = $this->roomRepo->findOneBy(['id' => $roomId]);

            // Récupération des utilisateurs de la salle et conversion en tableau
            $utilisateurs = $room->getUtilisateurs()->toArray();

            // Récupération de l'utilisateur courant
            /** @var Utilisateur $currentUser */
            $currentUser = $this->getUser();

            // Parcours des utilisateurs de la salle
            foreach ($utilisateurs as $utilisateur) {
                // Si l'ID de l'utilisateur n'est pas égal à l'ID de l'utilisateur courant, alors il est le destinataire
                if ($utilisateur->getId() !== $currentUser->getId()) {
                    $destinataire = $utilisateur;
                } else {
                    // Sinon, l'utilisateur courant est l'expéditeur
                    $expediteur = $utilisateur;
                }
            }
            // Création d'un nouveau message
            $message = new Message();
            // Définition de la salle du message
            $message->setRoom($room);
            // Définition du texte du message
            $message->setTexteMessage($text);
            // Définition de l'expéditeur du message
            $message->setExpediteur($expediteur);
            // Définition du destinataire du message
            $message->setDestinataire($destinataire);

            // Mise à jour du dernier message de la salle
            $room->setLastMessage($text);

            // Persistance du message dans la base de données
            $this->em->persist($message);
            // Enregistrement des changements dans la base de données
            $this->em->flush();

            // Retour du message en format JSON avec un code de statut 200
            return $this->json($message, 200, [], ['groups' => 'message']);
        } catch (\Throwable $th) {
            // En cas d'erreur, retour du message d'erreur en format JSON avec un code de statut 500
            return $this->json($th->getMessage(), 500);
        }
    }

    #[Route('/chat/create-room', name: 'chat_new_room', methods: ['POST'])]
    public function newRoom(Request $request, UtilisateurRepository $utilisateurRepo): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $userId = $data['userId'];

            /** @var Utilisateur $user */
            $me = $this->getUser();

            $him = $utilisateurRepo->find($userId);

            // check if room already exists
            $room = $this->roomRepo->findRoomByUsers($me, $him);

            if ($room) {
                return $this->json(['roomId' => $room->getId()]);
            }

            $room = new Room();
            $room->setLastMessage('');
            $room->addUtilisateur($me);
            $room->addUtilisateur($him);

            $this->em->persist($room);
            $this->em->flush();

            return $this->json(['roomId' => $room->getId()]);
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 400);
        }
    }
}
