<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320124209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(45) NOT NULL, parent_id INTEGER DEFAULT NULL, CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 ON categories (name)');
        $this->addSql('CREATE INDEX IDX_3AF34668727ACA70 ON categories (parent_id)');
        $this->addSql('CREATE TABLE "comments" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, author_id INTEGER NOT NULL, video_id INTEGER NOT NULL, CONSTRAINT FK_5F9E962AF675F31B FOREIGN KEY (author_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5F9E962A29C1004E FOREIGN KEY (video_id) REFERENCES videos (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5F9E962AF675F31B ON "comments" (author_id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A29C1004E ON "comments" (video_id)');
        $this->addSql('CREATE TABLE "subscriptions" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "plan" VARCHAR(255) NOT NULL, valid_to DATETIME NOT NULL, payment_status VARCHAR(45) DEFAULT NULL, free_plan_used BOOLEAN DEFAULT NULL)');
        $this->addSql('CREATE TABLE "users" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(45) NOT NULL, last_name VARCHAR(45) NOT NULL, vimeo_api_key VARCHAR(255) DEFAULT NULL, subscription_id INTEGER DEFAULT NULL, CONSTRAINT FK_1483A5E99A1887DC FOREIGN KEY (subscription_id) REFERENCES "subscriptions" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E99A1887DC ON "users" (subscription_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "users" (email)');
        $this->addSql('CREATE TABLE videos (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, duration INTEGER DEFAULT NULL, category_id INTEGER DEFAULT NULL, CONSTRAINT FK_29AA643212469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_29AA643212469DE2 ON videos (category_id)');
        $this->addSql('CREATE INDEX title_idx ON videos (title)');
        $this->addSql('CREATE TABLE likes (video_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY (video_id, user_id), CONSTRAINT FK_49CA4E7D29C1004E FOREIGN KEY (video_id) REFERENCES videos (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_49CA4E7DA76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_49CA4E7D29C1004E ON likes (video_id)');
        $this->addSql('CREATE INDEX IDX_49CA4E7DA76ED395 ON likes (user_id)');
        $this->addSql('CREATE TABLE dislikes (video_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY (video_id, user_id), CONSTRAINT FK_2DF3BE1129C1004E FOREIGN KEY (video_id) REFERENCES videos (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2DF3BE11A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2DF3BE1129C1004E ON dislikes (video_id)');
        $this->addSql('CREATE INDEX IDX_2DF3BE11A76ED395 ON dislikes (user_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE "comments"');
        $this->addSql('DROP TABLE "subscriptions"');
        $this->addSql('DROP TABLE "users"');
        $this->addSql('DROP TABLE videos');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE dislikes');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
