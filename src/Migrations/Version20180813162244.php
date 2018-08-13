<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180813162244 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE info ADD contact_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE info ADD CONSTRAINT FK_CB893157E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('CREATE INDEX IDX_CB893157E7A1254A ON info (contact_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE info DROP FOREIGN KEY FK_CB893157E7A1254A');
        $this->addSql('DROP INDEX IDX_CB893157E7A1254A ON info');
        $this->addSql('ALTER TABLE info DROP contact_id');
    }
}
