<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904085332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, appartenir_id INT NOT NULL, nom VARCHAR(255) NOT NULL, specificite LONGTEXT NOT NULL, INDEX IDX_8F87BF96E977E148 (appartenir_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96E977E148 FOREIGN KEY (appartenir_id) REFERENCES categorisation (id)');
        $this->addSql('ALTER TABLE espece ADD classe_id INT NOT NULL');
        $this->addSql('ALTER TABLE espece ADD CONSTRAINT FK_1A2A1B18F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('CREATE INDEX IDX_1A2A1B18F5EA509 ON espece (classe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE espece DROP FOREIGN KEY FK_1A2A1B18F5EA509');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96E977E148');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP INDEX IDX_1A2A1B18F5EA509 ON espece');
        $this->addSql('ALTER TABLE espece DROP classe_id');
    }
}
