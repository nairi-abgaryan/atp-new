<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190625104619 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(256) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE events ADD title VARCHAR(200) NOT NULL, ADD description VARCHAR(250) NOT NULL, ADD location VARCHAR(200) NOT NULL, ADD start_date DATETIME NOT NULL, ADD end_date DATETIME NOT NULL, DROP created_at, DROP updated_at, CHANGE image image VARCHAR(256) DEFAULT NULL');
        $this->addSql('ALTER TABLE eventsLang DROP FOREIGN KEY FK_8AC1A5F271F7E88B');
        $this->addSql('ALTER TABLE eventsLang ADD CONSTRAINT FK_8AC1A5F271F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE eventsLang DROP FOREIGN KEY FK_8AC1A5F271F7E88B');
        $this->addSql('DROP TABLE event');
        $this->addSql('ALTER TABLE events ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, DROP title, DROP description, DROP location, DROP start_date, DROP end_date, CHANGE image image VARCHAR(256) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE eventsLang DROP FOREIGN KEY FK_8AC1A5F271F7E88B');
        $this->addSql('ALTER TABLE eventsLang ADD CONSTRAINT FK_8AC1A5F271F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
