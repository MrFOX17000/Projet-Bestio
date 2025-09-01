<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250901135947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCF675F31B');
        $this->addSql('DROP INDEX IDX_67F068BCF675F31B ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP author_id');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E30DE8192');
        $this->addSql('DROP INDEX IDX_B6F7494E30DE8192 ON question');
        $this->addSql('ALTER TABLE question DROP poser_id');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, ADD is_verified TINYINT(1) NOT NULL, DROP role, DROP pseudo, DROP photo, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question ADD poser_id INT NOT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E30DE8192 FOREIGN KEY (poser_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B6F7494E30DE8192 ON question (poser_id)');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user ADD role VARCHAR(255) NOT NULL, ADD pseudo VARCHAR(255) NOT NULL, ADD photo VARCHAR(255) NOT NULL, DROP roles, DROP is_verified, CHANGE email email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCF675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_67F068BCF675F31B ON commentaire (author_id)');
    }
}
