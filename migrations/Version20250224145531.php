<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224145531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, image VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, organizer_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, date DATETIME NOT NULL, description CLOB NOT NULL, location VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_3BAE0AA7876C4DDA FOREIGN KEY (organizer_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7876C4DDA ON event (organizer_id)');
        $this->addSql('CREATE TABLE event_artist (event_id INTEGER NOT NULL, artist_id INTEGER NOT NULL, PRIMARY KEY(event_id, artist_id), CONSTRAINT FK_33C0E1D571F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_33C0E1D5B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_33C0E1D571F7E88B ON event_artist (event_id)');
        $this->addSql('CREATE INDEX IDX_33C0E1D5B7970CF8 ON event_artist (artist_id)');
        $this->addSql('CREATE TABLE event_user (event_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(event_id, user_id), CONSTRAINT FK_92589AE271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_92589AE2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_92589AE271F7E88B ON event_user (event_id)');
        $this->addSql('CREATE INDEX IDX_92589AE2A76ED395 ON event_user (user_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , profile_picture VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_artist');
        $this->addSql('DROP TABLE event_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
