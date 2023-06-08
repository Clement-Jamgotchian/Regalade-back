<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230608131300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fridge (id INT AUTO_INCREMENT NOT NULL, ingredient_id INT NOT NULL, user_id INT NOT NULL, quantity INT DEFAULT NULL, INDEX IDX_F2E94D89933FE08C (ingredient_id), INDEX IDX_F2E94D89A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fridge ADD CONSTRAINT FK_F2E94D89933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE fridge ADD CONSTRAINT FK_F2E94D89A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fridge DROP FOREIGN KEY FK_F2E94D89933FE08C');
        $this->addSql('ALTER TABLE fridge DROP FOREIGN KEY FK_F2E94D89A76ED395');
        $this->addSql('DROP TABLE fridge');
    }
}
