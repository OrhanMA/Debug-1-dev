<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411131416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C8369CCBE9A');
        $this->addSql('DROP INDEX IDX_31204C8369CCBE9A ON thread');
        $this->addSql('ALTER TABLE thread CHANGE author_id_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE
          thread
        ADD
          CONSTRAINT FK_31204C83F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_31204C83F675F31B ON thread (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C83F675F31B');
        $this->addSql('DROP INDEX IDX_31204C83F675F31B ON thread');
        $this->addSql('ALTER TABLE thread CHANGE author_id author_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE
          thread
        ADD
          CONSTRAINT FK_31204C8369CCBE9A FOREIGN KEY (author_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_31204C8369CCBE9A ON thread (author_id_id)');
    }
}
