<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613093920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE allergen (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE allergen_member (allergen_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_FCDDBB896E775A4A (allergen_id), INDEX IDX_FCDDBB897597D3FE (member_id), PRIMARY KEY(allergen_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE allergen_recipe (allergen_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_56B1F0C66E775A4A (allergen_id), INDEX IDX_56B1F0C659D8A214 (recipe_id), PRIMARY KEY(allergen_id, recipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE diet (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE diet_member (diet_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_8C2DE2E8E1E13ACE (diet_id), INDEX IDX_8C2DE2E87597D3FE (member_id), PRIMARY KEY(diet_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE diet_recipe (diet_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_2641A9A7E1E13ACE (diet_id), INDEX IDX_2641A9A759D8A214 (recipe_id), PRIMARY KEY(diet_id, recipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE allergen_member ADD CONSTRAINT FK_FCDDBB896E775A4A FOREIGN KEY (allergen_id) REFERENCES allergen (id)');
        $this->addSql('ALTER TABLE allergen_member ADD CONSTRAINT FK_FCDDBB897597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE allergen_recipe ADD CONSTRAINT FK_56B1F0C66E775A4A FOREIGN KEY (allergen_id) REFERENCES allergen (id)');
        $this->addSql('ALTER TABLE allergen_recipe ADD CONSTRAINT FK_56B1F0C659D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE diet_member ADD CONSTRAINT FK_8C2DE2E8E1E13ACE FOREIGN KEY (diet_id) REFERENCES diet (id)');
        $this->addSql('ALTER TABLE diet_member ADD CONSTRAINT FK_8C2DE2E87597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE diet_recipe ADD CONSTRAINT FK_2641A9A7E1E13ACE FOREIGN KEY (diet_id) REFERENCES diet (id)');
        $this->addSql('ALTER TABLE diet_recipe ADD CONSTRAINT FK_2641A9A759D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allergen_member DROP FOREIGN KEY FK_FCDDBB896E775A4A');
        $this->addSql('ALTER TABLE allergen_member DROP FOREIGN KEY FK_FCDDBB897597D3FE');
        $this->addSql('ALTER TABLE allergen_recipe DROP FOREIGN KEY FK_56B1F0C66E775A4A');
        $this->addSql('ALTER TABLE allergen_recipe DROP FOREIGN KEY FK_56B1F0C659D8A214');
        $this->addSql('ALTER TABLE diet_member DROP FOREIGN KEY FK_8C2DE2E8E1E13ACE');
        $this->addSql('ALTER TABLE diet_member DROP FOREIGN KEY FK_8C2DE2E87597D3FE');
        $this->addSql('ALTER TABLE diet_recipe DROP FOREIGN KEY FK_2641A9A7E1E13ACE');
        $this->addSql('ALTER TABLE diet_recipe DROP FOREIGN KEY FK_2641A9A759D8A214');
        $this->addSql('DROP TABLE allergen');
        $this->addSql('DROP TABLE allergen_member');
        $this->addSql('DROP TABLE allergen_recipe');
        $this->addSql('DROP TABLE diet');
        $this->addSql('DROP TABLE diet_member');
        $this->addSql('DROP TABLE diet_recipe');
    }
}
