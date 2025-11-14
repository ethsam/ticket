<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114072451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer ADD form_id INT NOT NULL, ADD data JSON NOT NULL, ADD ip VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A255FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('CREATE INDEX IDX_DADD4A255FF69B7D ON answer (form_id)');
        $this->addSql('ALTER TABLE field ADD form_id INT NOT NULL, ADD label VARCHAR(255) NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD type VARCHAR(50) NOT NULL, ADD required TINYINT(1) NOT NULL, ADD position INT NOT NULL, ADD options JSON DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF545585FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('CREATE INDEX IDX_5BF545585FF69B7D ON field (form_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A255FF69B7D');
        $this->addSql('DROP INDEX IDX_DADD4A255FF69B7D ON answer');
        $this->addSql('ALTER TABLE answer DROP form_id, DROP data, DROP ip');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF545585FF69B7D');
        $this->addSql('DROP INDEX IDX_5BF545585FF69B7D ON field');
        $this->addSql('ALTER TABLE field DROP form_id, DROP label, DROP name, DROP type, DROP required, DROP position, DROP options, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
