<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124121133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE result_cache ADD id INT AUTO_INCREMENT NOT NULL FIRST, ADD time_key VARCHAR(255) NOT NULL, CHANGE awardID awardID VARCHAR(30) DEFAULT NULL AFTER id, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D0B33C6BDC3A81857FC45F1D9505CCB95011C0C3 ON result_cache (awardID, filter, algorithm, time_key)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE result_cache MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_D0B33C6BDC3A81857FC45F1D9505CCB95011C0C3 ON result_cache');
        $this->addSql('DROP INDEX `PRIMARY` ON result_cache');
        $this->addSql('ALTER TABLE result_cache DROP id, DROP time_key, CHANGE awardID awardID VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE result_cache ADD PRIMARY KEY (filter, awardID, algorithm)');
    }
}
