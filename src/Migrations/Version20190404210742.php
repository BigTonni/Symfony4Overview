<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190404210742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE subscription_category (subscription_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_61E246ED9A1887DC (subscription_id), INDEX IDX_61E246ED12469DE2 (category_id), PRIMARY KEY(subscription_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription_category ADD CONSTRAINT FK_61E246ED9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription_category ADD CONSTRAINT FK_61E246ED12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A21214B7');
        $this->addSql('DROP INDEX IDX_A3C664D3A21214B7 ON subscription');
        $this->addSql('ALTER TABLE subscription DROP categories_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE subscription_category');
        $this->addSql('ALTER TABLE subscription ADD categories_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3A21214B7 ON subscription (categories_id)');
    }
}
