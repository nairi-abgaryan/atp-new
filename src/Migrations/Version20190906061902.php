<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190906061902 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ambassador_images (ambassador_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_29EC8B04A709FDF (ambassador_id), UNIQUE INDEX UNIQ_29EC8B03DA5256D (image_id), PRIMARY KEY(ambassador_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ambassador_images ADD CONSTRAINT FK_29EC8B04A709FDF FOREIGN KEY (ambassador_id) REFERENCES ambassadors (id)');
        $this->addSql('ALTER TABLE ambassador_images ADD CONSTRAINT FK_29EC8B03DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('DROP TABLE ambassador');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ambassador (ambassador_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_6C62F0CE4A709FDF (ambassador_id), UNIQUE INDEX UNIQ_6C62F0CE3DA5256D (image_id), PRIMARY KEY(ambassador_id, image_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ambassador ADD CONSTRAINT FK_6C62F0CE3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE ambassador ADD CONSTRAINT FK_6C62F0CE4A709FDF FOREIGN KEY (ambassador_id) REFERENCES ambassadors (id)');
        $this->addSql('DROP TABLE ambassador_images');
    }
}
