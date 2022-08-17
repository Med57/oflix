<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220406075927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE casting (id INT AUTO_INCREMENT NOT NULL, movie_id INT NOT NULL, actor_id INT NOT NULL, role VARCHAR(255) NOT NULL, credit_order INT NOT NULL, INDEX IDX_D11BBA508F93B6FC (movie_id), INDEX IDX_D11BBA5010DAF24A (actor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE casting ADD CONSTRAINT FK_MOVIE_CASTING FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE casting ADD CONSTRAINT FK_ACTOR_CASTING FOREIGN KEY (actor_id) REFERENCES actor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE casting');
    }
}
