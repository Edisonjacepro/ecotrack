<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260127141000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for users, carbon records, eco actions, and user actions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(120) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');

        $this->addSql('CREATE TABLE eco_action (id SERIAL NOT NULL, title VARCHAR(160) NOT NULL, description TEXT NOT NULL, category VARCHAR(60) NOT NULL, estimated_saving_kg DOUBLE PRECISION NOT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');

        $this->addSql('CREATE TABLE carbon_record (id SERIAL NOT NULL, user_id INT DEFAULT NULL, category VARCHAR(60) NOT NULL, amount_kg DOUBLE PRECISION NOT NULL, recorded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, source_data JSON DEFAULT NULL, notes TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5DB22506A76ED395 ON carbon_record (user_id)');
        $this->addSql('ALTER TABLE carbon_record ADD CONSTRAINT FK_5DB22506A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE TABLE user_eco_action (id SERIAL NOT NULL, user_id INT DEFAULT NULL, eco_action_id INT DEFAULT NULL, status VARCHAR(40) NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, notes TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3E5C9D4CA76ED395 ON user_eco_action (user_id)');
        $this->addSql('CREATE INDEX IDX_3E5C9D4C1E1C1235 ON user_eco_action (eco_action_id)');
        $this->addSql('ALTER TABLE user_eco_action ADD CONSTRAINT FK_3E5C9D4CA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_eco_action ADD CONSTRAINT FK_3E5C9D4C1E1C1235 FOREIGN KEY (eco_action_id) REFERENCES eco_action (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE carbon_record DROP CONSTRAINT FK_5DB22506A76ED395');
        $this->addSql('ALTER TABLE user_eco_action DROP CONSTRAINT FK_3E5C9D4CA76ED395');
        $this->addSql('ALTER TABLE user_eco_action DROP CONSTRAINT FK_3E5C9D4C1E1C1235');
        $this->addSql('DROP TABLE carbon_record');
        $this->addSql('DROP TABLE user_eco_action');
        $this->addSql('DROP TABLE eco_action');
        $this->addSql('DROP TABLE "user"');
    }
}
