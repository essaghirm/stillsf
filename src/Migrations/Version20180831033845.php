<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180831033845 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, lft INT DEFAULT NULL, rgt INT DEFAULT NULL, lvl INT DEFAULT NULL, old_id INT DEFAULT NULL, old_parent INT DEFAULT NULL, INDEX IDX_64C19C1727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, fname VARCHAR(255) NOT NULL, lname VARCHAR(255) DEFAULT NULL, web_site VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, notes LONGTEXT DEFAULT NULL, type VARCHAR(25) NOT NULL, created DATETIME DEFAULT NULL, INDEX IDX_4C62E63812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info (id INT AUTO_INCREMENT NOT NULL, contact_id INT DEFAULT NULL, type VARCHAR(50) NOT NULL, label VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_CB893157E7A1254A (contact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE relation (id INT AUTO_INCREMENT NOT NULL, contact_id INT NOT NULL, friend_id INT NOT NULL, occupation VARCHAR(255) DEFAULT NULL, INDEX IDX_62894749E7A1254A (contact_id), INDEX IDX_628947496A5458E8 (friend_id), UNIQUE INDEX IDX_UNQ_CONTACT_FRIEND (contact_id, friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE old_category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, old_id INT DEFAULT NULL, parent INT DEFAULT NULL, `table` INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, user_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, status INT NOT NULL, roles VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE info ADD CONSTRAINT FK_CB893157E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('ALTER TABLE relation ADD CONSTRAINT FK_62894749E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('ALTER TABLE relation ADD CONSTRAINT FK_628947496A5458E8 FOREIGN KEY (friend_id) REFERENCES contact (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63812469DE2');
        $this->addSql('ALTER TABLE info DROP FOREIGN KEY FK_CB893157E7A1254A');
        $this->addSql('ALTER TABLE relation DROP FOREIGN KEY FK_62894749E7A1254A');
        $this->addSql('ALTER TABLE relation DROP FOREIGN KEY FK_628947496A5458E8');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE info');
        $this->addSql('DROP TABLE relation');
        $this->addSql('DROP TABLE old_category');
        $this->addSql('DROP TABLE user');
    }
}
