<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231117093041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pseudo_en_jeu (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, jeu_id INT NOT NULL, pseudo VARCHAR(50) NOT NULL, INDEX IDX_9A9EFAD3FB88E14F (utilisateur_id), INDEX IDX_9A9EFAD38C9E392E (jeu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pseudo_en_jeu ADD CONSTRAINT FK_9A9EFAD3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE pseudo_en_jeu ADD CONSTRAINT FK_9A9EFAD38C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pseudo_en_jeu DROP FOREIGN KEY FK_9A9EFAD3FB88E14F');
        $this->addSql('ALTER TABLE pseudo_en_jeu DROP FOREIGN KEY FK_9A9EFAD38C9E392E');
        $this->addSql('DROP TABLE pseudo_en_jeu');
    }
}
