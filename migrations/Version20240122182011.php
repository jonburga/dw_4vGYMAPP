<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240122182011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, activity_type_id INT NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME NOT NULL, INDEX IDX_AC74095AC51EFA73 (activity_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_monitor (activity_id INT NOT NULL, monitor_id INT NOT NULL, INDEX IDX_E147EF6581C06096 (activity_id), INDEX IDX_E147EF654CE1C902 (monitor_id), PRIMARY KEY(activity_id, monitor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, numbermonitors INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monitor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monitor_activity (monitor_id INT NOT NULL, activity_id INT NOT NULL, INDEX IDX_7647DDAE4CE1C902 (monitor_id), INDEX IDX_7647DDAE81C06096 (activity_id), PRIMARY KEY(monitor_id, activity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AC51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (id)');
        $this->addSql('ALTER TABLE activity_monitor ADD CONSTRAINT FK_E147EF6581C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_monitor ADD CONSTRAINT FK_E147EF654CE1C902 FOREIGN KEY (monitor_id) REFERENCES monitor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE monitor_activity ADD CONSTRAINT FK_7647DDAE4CE1C902 FOREIGN KEY (monitor_id) REFERENCES monitor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE monitor_activity ADD CONSTRAINT FK_7647DDAE81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AC51EFA73');
        $this->addSql('ALTER TABLE activity_monitor DROP FOREIGN KEY FK_E147EF6581C06096');
        $this->addSql('ALTER TABLE activity_monitor DROP FOREIGN KEY FK_E147EF654CE1C902');
        $this->addSql('ALTER TABLE monitor_activity DROP FOREIGN KEY FK_7647DDAE4CE1C902');
        $this->addSql('ALTER TABLE monitor_activity DROP FOREIGN KEY FK_7647DDAE81C06096');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_monitor');
        $this->addSql('DROP TABLE activity_type');
        $this->addSql('DROP TABLE monitor');
        $this->addSql('DROP TABLE monitor_activity');
    }
}
