<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211005070245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF7E73ABD');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8DF7E73ABD ON post');
        $this->addSql('ALTER TABLE post DROP image2_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD image2_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF7E73ABD FOREIGN KEY (image2_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8DF7E73ABD ON post (image2_id)');
    }
}
