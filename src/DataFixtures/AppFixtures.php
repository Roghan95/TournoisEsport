<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Utilisateur;
use App\Entity\Jeu;
use App\Entity\Tournoi;
use App\Entity\Room;
use App\Entity\GameMatch;
use App\Entity\Message;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Génération de données fictives pour Utilisateur
        $usersList = [];
        for ($i = 0; $i < 25; $i++) {
            $user = new Utilisateur();
            $user->setEmail($faker->email);
            $user->setPseudo($faker->userName);
            $user->setPassword($faker->password);
            $user->setPhoto($faker->imageUrl(255, 255, 'people'));
            $user->setIsVerified(true);
            // Ajoutez d'autres propriétés d'utilisateur selon vos besoins
            $manager->persist($user);
            array_push($usersList, $user);
        }

        // Génération de données fictives pour Jeu
        for ($i = 0; $i < 5; $i++) {
            $jeu = new Jeu();
            $jeu->setNomJeu($faker->words(2, true));
            $jeu->setLogo($faker->imageUrl(255, 255, 'game'));

            // Ajoutez d'autres propriétés de jeu selon vos besoins
            $manager->persist($jeu);
        }

        // Génération de données fictives pour Tournoi
        for ($i = 0; $i < 5; $i++) {
            $tournoi = new Tournoi();
            $tournoi->setNomTournoi($faker->words(3, true));
            $tournoi->setNomOrganisation($faker->company);
            $tournoi->setNbJoueurMax($faker->numberBetween(2, 10));
            $tournoi->setJeu($jeu);
            $tournoi->setBanniereTr($faker->imageUrl(255, 255, 'tournament'));
            $tournoi->setLienTwitch($faker->url);
            $tournoi->setOrganisateur($usersList[$i]);
            $tournoi->setDateDebut(new \DateTimeImmutable());
            $tournoi->setDateFin(new \DateTimeImmutable());
            $tournoi->setDescription($faker->text);

            // Ajoutez d'autres propriétés de tournoi selon vos besoins
            $manager->persist($tournoi);

            // Génération de données fictives pour GameMatch
            for ($j = 0; $j < 5; $j++) {
                $gameMatch = new GameMatch();
                $gameMatch->setStatut($faker->boolean);
                $gameMatch->setDateDebut($faker->dateTimeThisMonth);
                $gameMatch->setTournoi($tournoi);
                // Ajoutez d'autres propriétés de gameMatch selon vos besoins
                $manager->persist($gameMatch);
            }
        }

        // Génération de données fictives pour Room
        for ($i = 0; $i < 3; $i++) {
            $room = new Room();
            $room->setLastMessage($faker->sentence);
            $room->addUtilisateur($usersList[$i]);
            // Ajoutez d'autres propriétés de room selon vos besoins
            $manager->persist($room);

            // Génération de données fictives pour Message
            for ($j = 0; $j < 20; $j++) {
                $message = new Message();
                $message->setTexteMessage($faker->paragraph);
                $message->setRoom($room);
                $message->setExpediteur($usersList[$j]);

                // Assurez-vous que le destinataire est différent de l'expéditeur
                do {
                    $destinataireIndex = $faker->numberBetween(0, count($usersList) - 1);
                } while ($destinataireIndex === $i);

                $message->setDestinataire($usersList[$destinataireIndex]);

                $manager->persist($message);
            }
        }




        $manager->flush();
    }
}
