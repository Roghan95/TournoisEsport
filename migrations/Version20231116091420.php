<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231116091420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_match ADD type_match VARCHAR(255) NOT NULL, ADD nb_joueurs_max INT NOT NULL');
        $this->addSql('ALTER TABLE tournoi DROP nb_joueur_max');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_match DROP type_match, DROP nb_joueurs_max');
        $this->addSql('ALTER TABLE tournoi ADD nb_joueur_max INT NOT NULL');
    }
}
