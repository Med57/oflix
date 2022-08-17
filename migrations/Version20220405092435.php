<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405092435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movie_genre (movie_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_FD1229648F93B6FC (movie_id), INDEX IDX_FD1229644296D31F (genre_id), PRIMARY KEY(movie_id, genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movie_genre ADD CONSTRAINT FK_MOVIE_GENRE FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_genre ADD CONSTRAINT FK_GENRE_MOVIE FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_SEASON_MOVIE');
        $this->addSql('DROP INDEX idx_season_movie ON season');
        $this->addSql('CREATE INDEX IDX_F0E45BA98F93B6FC ON season (movie_id)');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_SEASON_MOVIE FOREIGN KEY (movie_id) REFERENCES movie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE movie_genre');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA98F93B6FC');
        $this->addSql('DROP INDEX idx_f0e45ba98f93b6fc ON season');
        $this->addSql('CREATE INDEX IDX_SEASON_MOVIE ON season (movie_id)');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA98F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
    }
}
