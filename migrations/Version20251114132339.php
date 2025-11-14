<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114132339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form ADD sendmailbool TINYINT(1) DEFAULT NULL, ADD sendmailto VARCHAR(255) DEFAULT NULL, ADD gmail_pass_application VARCHAR(255) DEFAULT NULL, ADD smtpserver VARCHAR(255) DEFAULT NULL, ADD smtplogin VARCHAR(255) DEFAULT NULL, ADD smtppassword VARCHAR(255) DEFAULT NULL, ADD smtpport VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form DROP sendmailbool, DROP sendmailto, DROP gmail_pass_application, DROP smtpserver, DROP smtplogin, DROP smtppassword, DROP smtpport');
    }
}
