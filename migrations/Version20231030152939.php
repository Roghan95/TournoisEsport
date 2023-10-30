<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231030152939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_utilisateur (room_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_929CD8C54177093 (room_id), INDEX IDX_929CD8CFB88E14F (utilisateur_id), PRIMARY KEY(room_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room_utilisateur ADD CONSTRAINT FK_929CD8C54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_utilisateur ADD CONSTRAINT FK_929CD8CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room_utilisateur DROP FOREIGN KEY FK_929CD8C54177093');
        $this->addSql('ALTER TABLE room_utilisateur DROP FOREIGN KEY FK_929CD8CFB88E14F');
        $this->addSql('DROP TABLE room_utilisateur');
    }
}
