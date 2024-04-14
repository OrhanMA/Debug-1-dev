<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240414164913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A1085649D86650F');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564D6DE06A6');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856475C0816C');
        $this->addSql('DROP INDEX IDX_5A1085649D86650F ON vote');
        $this->addSql('DROP INDEX IDX_5A108564D6DE06A6 ON vote');
        $this->addSql('DROP INDEX IDX_5A10856475C0816C ON vote');
        $this->addSql('ALTER TABLE vote ADD thread_id INT DEFAULT NULL, ADD comment_id INT DEFAULT NULL, DROP thread_id_id, DROP comment_id_id, CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564E2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_5A108564A76ED395 ON vote (user_id)');
        $this->addSql('CREATE INDEX IDX_5A108564E2904019 ON vote (thread_id)');
        $this->addSql('CREATE INDEX IDX_5A108564F8697D13 ON vote (comment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564A76ED395');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564E2904019');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564F8697D13');
        $this->addSql('DROP INDEX IDX_5A108564A76ED395 ON vote');
        $this->addSql('DROP INDEX IDX_5A108564E2904019 ON vote');
        $this->addSql('DROP INDEX IDX_5A108564F8697D13 ON vote');
        $this->addSql('ALTER TABLE vote ADD thread_id_id INT DEFAULT NULL, ADD comment_id_id INT DEFAULT NULL, DROP thread_id, DROP comment_id, CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A1085649D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564D6DE06A6 FOREIGN KEY (comment_id_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856475C0816C FOREIGN KEY (thread_id_id) REFERENCES thread (id)');
        $this->addSql('CREATE INDEX IDX_5A1085649D86650F ON vote (user_id_id)');
        $this->addSql('CREATE INDEX IDX_5A108564D6DE06A6 ON vote (comment_id_id)');
        $this->addSql('CREATE INDEX IDX_5A10856475C0816C ON vote (thread_id_id)');
    }
}
