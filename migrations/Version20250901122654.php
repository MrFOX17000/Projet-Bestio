<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250901122654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorisation (id INT AUTO_INCREMENT NOT NULL, nom_categorisation VARCHAR(255) NOT NULL, specificite LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, author_id INT NOT NULL, contenu LONGTEXT NOT NULL, created_at_comm DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_67F068BC1E27F6BF (question_id), INDEX IDX_67F068BCF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE espece (id INT AUTO_INCREMENT NOT NULL, appartenir_id INT NOT NULL, nom_espece VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, taille_moy DOUBLE PRECISION NOT NULL, poids_moy INT NOT NULL, gestation INT NOT NULL, esperance_vie INT NOT NULL, habitat VARCHAR(255) NOT NULL, alimentation VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_1A2A1B1E977E148 (appartenir_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, espece_id INT NOT NULL, poser_id INT NOT NULL, titre_question VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B6F7494E2D191E7A (espece_id), INDEX IDX_B6F7494E30DE8192 (poser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE race (id INT AUTO_INCREMENT NOT NULL, espece_id INT NOT NULL, nom_race VARCHAR(255) NOT NULL, specificite LONGTEXT NOT NULL, INDEX IDX_DA6FBBAF2D191E7A (espece_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE espece ADD CONSTRAINT FK_1A2A1B1E977E148 FOREIGN KEY (appartenir_id) REFERENCES categorisation (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E2D191E7A FOREIGN KEY (espece_id) REFERENCES espece (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E30DE8192 FOREIGN KEY (poser_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE race ADD CONSTRAINT FK_DA6FBBAF2D191E7A FOREIGN KEY (espece_id) REFERENCES espece (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC1E27F6BF');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCF675F31B');
        $this->addSql('ALTER TABLE espece DROP FOREIGN KEY FK_1A2A1B1E977E148');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E2D191E7A');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E30DE8192');
        $this->addSql('ALTER TABLE race DROP FOREIGN KEY FK_DA6FBBAF2D191E7A');
        $this->addSql('DROP TABLE categorisation');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE espece');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
