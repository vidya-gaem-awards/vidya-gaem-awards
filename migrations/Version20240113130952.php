<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240113130952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE captcha_games (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, first VARCHAR(255) NOT NULL, second VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_73FCE9813DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE captcha_games ADD CONSTRAINT FK_73FCE9813DA5256D FOREIGN KEY (image_id) REFERENCES files (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE captcha_games DROP FOREIGN KEY FK_73FCE9813DA5256D');
        $this->addSql('DROP TABLE captcha_games');
    }
}
