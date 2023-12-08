<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231208101810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant_tournoi ADD equipe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant_tournoi ADD CONSTRAINT FK_82A216D76D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('CREATE INDEX IDX_82A216D76D861B89 ON participant_tournoi (equipe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant_tournoi DROP FOREIGN KEY FK_82A216D76D861B89');
        $this->addSql('DROP INDEX IDX_82A216D76D861B89 ON participant_tournoi');
        $this->addSql('ALTER TABLE participant_tournoi DROP equipe_id');
    }
}
