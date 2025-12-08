<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251213215959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE question (id UUID NOT NULL, content VARCHAR(2000) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN question.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN question.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE question_answer (id UUID NOT NULL, question_id UUID NOT NULL, content VARCHAR(2000) NOT NULL, correct BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DD80652D1E27F6BF ON question_answer (question_id)');
        $this->addSql('COMMENT ON COLUMN question_answer.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN question_answer.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN question_answer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quiz (id UUID NOT NULL, questions_id UUID DEFAULT NULL, configuration_questions_count INT NOT NULL, configuration_duration INT NOT NULL, configuration_passing_score INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A412FA92BCB134CE ON quiz (questions_id)');
        $this->addSql('COMMENT ON COLUMN quiz.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quiz.questions_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE quiz_answer (id UUID NOT NULL, question_id UUID DEFAULT NULL, content VARCHAR(2000) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3799BA7C1E27F6BF ON quiz_answer (question_id)');
        $this->addSql('COMMENT ON COLUMN quiz_answer.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quiz_answer.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE quiz_question (id UUID NOT NULL, question_id UUID NOT NULL, answer_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6033B00B1E27F6BF ON quiz_question (question_id)');
        $this->addSql('CREATE INDEX IDX_6033B00BAA334807 ON quiz_question (answer_id)');
        $this->addSql('COMMENT ON COLUMN quiz_question.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quiz_question.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quiz_question.answer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE question_answer ADD CONSTRAINT FK_DD80652D1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92BCB134CE FOREIGN KEY (questions_id) REFERENCES quiz_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_answer ADD CONSTRAINT FK_3799BA7C1E27F6BF FOREIGN KEY (question_id) REFERENCES quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_question ADD CONSTRAINT FK_6033B00B1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_question ADD CONSTRAINT FK_6033B00BAA334807 FOREIGN KEY (answer_id) REFERENCES question_answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE question_answer DROP CONSTRAINT FK_DD80652D1E27F6BF');
        $this->addSql('ALTER TABLE quiz DROP CONSTRAINT FK_A412FA92BCB134CE');
        $this->addSql('ALTER TABLE quiz_answer DROP CONSTRAINT FK_3799BA7C1E27F6BF');
        $this->addSql('ALTER TABLE quiz_question DROP CONSTRAINT FK_6033B00B1E27F6BF');
        $this->addSql('ALTER TABLE quiz_question DROP CONSTRAINT FK_6033B00BAA334807');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE question_answer');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE quiz_answer');
        $this->addSql('DROP TABLE quiz_question');
    }
}
