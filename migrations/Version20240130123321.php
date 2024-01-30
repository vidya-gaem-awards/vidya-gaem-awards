<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130123321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `primary` ON result_cache');
        $this->addSql('ALTER TABLE result_cache ADD algorithm VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE result_cache ADD PRIMARY KEY (filter, awardID, algorithm)');
        $this->addSql('UPDATE result_cache SET algorithm = \'schulze-legacy\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `PRIMARY` ON result_cache');
        $this->addSql('ALTER TABLE result_cache DROP algorithm');
        $this->addSql('ALTER TABLE result_cache ADD PRIMARY KEY (filter, awardID)');
    }
}
