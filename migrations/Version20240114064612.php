<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240114064612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rpg_characters (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, cookie_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E61F7B6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rpg_characters ADD CONSTRAINT FK_E61F7B6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE captcha_games RENAME INDEX idx_73fce9813da5256d TO IDX_81C280543DA5256D');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rpg_characters DROP FOREIGN KEY FK_E61F7B6A76ED395');
        $this->addSql('DROP TABLE rpg_characters');
        $this->addSql('ALTER TABLE captcha_games RENAME INDEX idx_81c280543da5256d TO IDX_73FCE9813DA5256D');
    }
}
