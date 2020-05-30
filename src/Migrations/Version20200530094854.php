<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200530094854 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE products CHANGE category_id category_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partners CHANGE link link VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE files DROP INDEX IDX_63540594584665A, ADD UNIQUE INDEX UNIQ_63540594584665A (product_id)');
        $this->addSql('ALTER TABLE files DROP INDEX IDX_6354059A76ED395, ADD UNIQUE INDEX UNIQ_6354059A76ED395 (user_id)');
        $this->addSql('ALTER TABLE files CHANGE product_id product_id INT NOT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE file_name file_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE files DROP INDEX UNIQ_63540594584665A, ADD INDEX IDX_63540594584665A (product_id)');
        $this->addSql('ALTER TABLE files DROP INDEX UNIQ_6354059A76ED395, ADD INDEX IDX_6354059A76ED395 (user_id)');
        $this->addSql('ALTER TABLE files CHANGE product_id product_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE file_name file_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE partners CHANGE link link VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE products CHANGE category_id category_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
