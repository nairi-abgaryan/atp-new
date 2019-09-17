<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190913110448 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE lessons_lang (id INT AUTO_INCREMENT NOT NULL, lessons_id INT DEFAULT NULL, lang VARCHAR(2) DEFAULT NULL, title LONGTEXT DEFAULT NULL, text LONGTEXT DEFAULT NULL, INDEX IDX_56A4E5B3FED07355 (lessons_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lessons (id INT AUTO_INCREMENT NOT NULL, pdf VARCHAR(256) DEFAULT NULL, pdf_name VARCHAR(256) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, image VARCHAR(256) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lessons_image (lessons_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_348E4B7CFED07355 (lessons_id), INDEX IDX_348E4B7C3DA5256D (image_id), PRIMARY KEY(lessons_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lessons_lang ADD CONSTRAINT FK_56A4E5B3FED07355 FOREIGN KEY (lessons_id) REFERENCES lessons (id)');
        $this->addSql('ALTER TABLE lessons_image ADD CONSTRAINT FK_348E4B7CFED07355 FOREIGN KEY (lessons_id) REFERENCES lessons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lessons_image ADD CONSTRAINT FK_348E4B7C3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teams CHANGE branch_id branch_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE lessons_lang DROP FOREIGN KEY FK_56A4E5B3FED07355');
        $this->addSql('ALTER TABLE lessons_image DROP FOREIGN KEY FK_348E4B7CFED07355');
        $this->addSql('DROP TABLE lessons_lang');
        $this->addSql('DROP TABLE lessons');
        $this->addSql('DROP TABLE lessons_image');
        $this->addSql('ALTER TABLE teams CHANGE branch_id branch_id INT NOT NULL');
    }
}
