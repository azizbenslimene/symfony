<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231113190507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_admin_event_user (event_admin_id INT NOT NULL, event_user_id INT NOT NULL, INDEX IDX_7C76DFAF22C76BC0 (event_admin_id), INDEX IDX_7C76DFAF22397A3A (event_user_id), PRIMARY KEY(event_admin_id, event_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_admin_event_user ADD CONSTRAINT FK_7C76DFAF22C76BC0 FOREIGN KEY (event_admin_id) REFERENCES event_admin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_admin_event_user ADD CONSTRAINT FK_7C76DFAF22397A3A FOREIGN KEY (event_user_id) REFERENCES event_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_admin_event_user DROP FOREIGN KEY FK_7C76DFAF22C76BC0');
        $this->addSql('ALTER TABLE event_admin_event_user DROP FOREIGN KEY FK_7C76DFAF22397A3A');
        $this->addSql('DROP TABLE event_admin_event_user');
    }
}
