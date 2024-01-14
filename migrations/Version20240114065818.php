<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240114065818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rpg_characters DROP FOREIGN KEY FK_E61F7B6A76ED395');
        $this->addSql('DROP INDEX IDX_E61F7B6A76ED395 ON rpg_characters');
        $this->addSql('ALTER TABLE rpg_characters DROP user_id, CHANGE cookie_id user VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rpg_characters ADD user_id INT DEFAULT NULL, CHANGE user cookie_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rpg_characters ADD CONSTRAINT FK_E61F7B6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E61F7B6A76ED395 ON rpg_characters (user_id)');
    }
}
