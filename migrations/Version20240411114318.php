<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411114318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (
          id INT AUTO_INCREMENT NOT NULL,
          name VARCHAR(15) NOT NULL,
          description VARCHAR(100) DEFAULT NULL,
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE COMMENT (
          id INT AUTO_INCREMENT NOT NULL,
          user_id INT NOT NULL,
          thread_id INT NOT NULL,
          content LONGTEXT NOT NULL,
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          is_solution TINYINT(1) NOT NULL,
          INDEX IDX_9474526CA76ED395 (user_id),
          INDEX IDX_9474526CE2904019 (thread_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thread_category (
          thread_id INT NOT NULL,
          category_id INT NOT NULL,
          INDEX IDX_9FD5A1DE2904019 (thread_id),
          INDEX IDX_9FD5A1D12469DE2 (category_id),
          PRIMARY KEY(thread_id, category_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE
          COMMENT
        ADD
          CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE
          COMMENT
        ADD
          CONSTRAINT FK_9474526CE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE
          thread_category
        ADD
          CONSTRAINT FK_9FD5A1DE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE
          thread_category
        ADD
          CONSTRAINT FK_9FD5A1D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE
          thread
        ADD
          author_id_id INT NOT NULL,
        DROP
          author,
        DROP
          catogories,
        CHANGE
          STATUS STATUS VARCHAR(7) NOT NULL');
        $this->addSql('ALTER TABLE
          thread
        ADD
          CONSTRAINT FK_31204C8369CCBE9A FOREIGN KEY (author_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_31204C8369CCBE9A ON thread (author_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE2904019');
        $this->addSql('ALTER TABLE thread_category DROP FOREIGN KEY FK_9FD5A1DE2904019');
        $this->addSql('ALTER TABLE thread_category DROP FOREIGN KEY FK_9FD5A1D12469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE thread_category');
        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C8369CCBE9A');
        $this->addSql('DROP INDEX IDX_31204C8369CCBE9A ON thread');
        $this->addSql('ALTER TABLE
          thread
        ADD
          author VARCHAR(50) NOT NULL,
        ADD
          catogories VARCHAR(255) DEFAULT NULL,
        DROP
          author_id_id,
        CHANGE
          STATUS STATUS VARCHAR(10) NOT NULL');
    }
}
