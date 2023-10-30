<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231030151754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_match ADD tournoi_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_match ADD CONSTRAINT FK_4868BC8AF607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id)');
        $this->addSql('CREATE INDEX IDX_4868BC8AF607770A ON game_match (tournoi_id)');
        $this->addSql('ALTER TABLE tournoi ADD jeu_id INT NOT NULL');
        $this->addSql('ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DF8C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id)');
        $this->addSql('CREATE INDEX IDX_18AFD9DF8C9E392E ON tournoi (jeu_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournoi DROP FOREIGN KEY FK_18AFD9DF8C9E392E');
        $this->addSql('DROP INDEX IDX_18AFD9DF8C9E392E ON tournoi');
        $this->addSql('ALTER TABLE tournoi DROP jeu_id');
        $this->addSql('ALTER TABLE game_match DROP FOREIGN KEY FK_4868BC8AF607770A');
        $this->addSql('DROP INDEX IDX_4868BC8AF607770A ON game_match');
        $this->addSql('ALTER TABLE game_match DROP tournoi_id');
    }
}
