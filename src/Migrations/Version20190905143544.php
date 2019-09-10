<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190905143544 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ambassador (ambassador_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_6C62F0CE4A709FDF (ambassador_id), UNIQUE INDEX UNIQ_6C62F0CE3DA5256D (image_id), PRIMARY KEY(ambassador_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ambassador ADD CONSTRAINT FK_6C62F0CE4A709FDF FOREIGN KEY (ambassador_id) REFERENCES ambassadors (id)');
        $this->addSql('ALTER TABLE ambassador ADD CONSTRAINT FK_6C62F0CE3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE teamsLang ADD text LONGTEXT DEFAULT NULL, DROP name, CHANGE position title LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE ambassadorsLang CHANGE text_bottom text_bottom LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ambassador');
        $this->addSql('ALTER TABLE ambassadorsLang CHANGE text_bottom text_bottom LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE teamsLang ADD name LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, ADD position LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP title, DROP text');
    }
}
