<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109074103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification ADD equipe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA6D861B89 ON notification (equipe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA6D861B89');
        $this->addSql('DROP INDEX IDX_BF5476CA6D861B89 ON notification');
        $this->addSql('ALTER TABLE notification DROP equipe_id');
    }
}
