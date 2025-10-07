<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251007103818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE phone ADD created_at DATETIME, ADD updated_at DATETIME');
        $this->addSql('UPDATE phone set created_at = NOW()');
        $this->addSql('UPDATE phone set updated_at = NOW()');
        $this->addSql('ALTER TABLE computer ADD created_at DATETIME, ADD updated_at DATETIME');
        $this->addSql('UPDATE computer set created_at = NOW()');
        $this->addSql('UPDATE computer set updated_at = NOW()');
        $this->addSql('ALTER TABLE computer CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE phone CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE phone DROP created_at, DROP updated_at');
    }
}
