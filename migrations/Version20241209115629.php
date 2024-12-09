<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209115629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tblProductData (int_product_data_id INT AUTO_INCREMENT NOT NULL, str_product_name VARCHAR(50) NOT NULL, str_product_desc VARCHAR(255) NOT NULL, str_product_code VARCHAR(10) NOT NULL, dtm_added DATETIME DEFAULT NULL, dtm_discontinued DATETIME DEFAULT NULL, stm_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_2C11248632E6B28B (str_product_code), PRIMARY KEY(int_product_data_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tblProductData');
    }
}
