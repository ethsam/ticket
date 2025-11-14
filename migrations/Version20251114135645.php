<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114135645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form DROP FOREIGN KEY FK_5288FD4FBE8CD577');
        $this->addSql('DROP INDEX IDX_5288FD4FBE8CD577 ON form');
        $this->addSql('ALTER TABLE form DROP mail_provider_id, DROP gmail_pass_application, DROP smtpserver, DROP smtplogin, DROP smtppassword, DROP smtpport');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form ADD mail_provider_id INT DEFAULT NULL, ADD gmail_pass_application VARCHAR(255) DEFAULT NULL, ADD smtpserver VARCHAR(255) DEFAULT NULL, ADD smtplogin VARCHAR(255) DEFAULT NULL, ADD smtppassword VARCHAR(255) DEFAULT NULL, ADD smtpport VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE form ADD CONSTRAINT FK_5288FD4FBE8CD577 FOREIGN KEY (mail_provider_id) REFERENCES mail_provider (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5288FD4FBE8CD577 ON form (mail_provider_id)');
    }
}
