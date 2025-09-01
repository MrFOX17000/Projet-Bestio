<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250901143136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_67F068BCF675F31B ON commentaire (author_id)');
        $this->addSql('ALTER TABLE user ADD pseudo VARCHAR(255) NOT NULL, ADD photo VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP pseudo, DROP photo');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCF675F31B');
        $this->addSql('DROP INDEX IDX_67F068BCF675F31B ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP author_id');
    }
}
