<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606090933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contains_ingredient (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, ingredient_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_271CBBA859D8A214 (recipe_id), INDEX IDX_271CBBA8933FE08C (ingredient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contains_ingredient ADD CONSTRAINT FK_271CBBA859D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE contains_ingredient ADD CONSTRAINT FK_271CBBA8933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contains_ingredient DROP FOREIGN KEY FK_271CBBA859D8A214');
        $this->addSql('ALTER TABLE contains_ingredient DROP FOREIGN KEY FK_271CBBA8933FE08C');
        $this->addSql('DROP TABLE contains_ingredient');
    }
}
