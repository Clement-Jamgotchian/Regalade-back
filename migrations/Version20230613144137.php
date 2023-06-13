<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613144137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allergen_member DROP FOREIGN KEY FK_FCDDBB896E775A4A');
        $this->addSql('ALTER TABLE allergen_member DROP FOREIGN KEY FK_FCDDBB897597D3FE');
        $this->addSql('ALTER TABLE allergen_member ADD CONSTRAINT FK_FCDDBB896E775A4A FOREIGN KEY (allergen_id) REFERENCES allergen (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE allergen_member ADD CONSTRAINT FK_FCDDBB897597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE allergen_recipe DROP FOREIGN KEY FK_56B1F0C66E775A4A');
        $this->addSql('ALTER TABLE allergen_recipe DROP FOREIGN KEY FK_56B1F0C659D8A214');
        $this->addSql('ALTER TABLE allergen_recipe ADD CONSTRAINT FK_56B1F0C66E775A4A FOREIGN KEY (allergen_id) REFERENCES allergen (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE allergen_recipe ADD CONSTRAINT FK_56B1F0C659D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE diet_member DROP FOREIGN KEY FK_8C2DE2E87597D3FE');
        $this->addSql('ALTER TABLE diet_member DROP FOREIGN KEY FK_8C2DE2E8E1E13ACE');
        $this->addSql('ALTER TABLE diet_member ADD CONSTRAINT FK_8C2DE2E87597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE diet_member ADD CONSTRAINT FK_8C2DE2E8E1E13ACE FOREIGN KEY (diet_id) REFERENCES diet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE diet_recipe DROP FOREIGN KEY FK_2641A9A759D8A214');
        $this->addSql('ALTER TABLE diet_recipe DROP FOREIGN KEY FK_2641A9A7E1E13ACE');
        $this->addSql('ALTER TABLE diet_recipe ADD CONSTRAINT FK_2641A9A759D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE diet_recipe ADD CONSTRAINT FK_2641A9A7E1E13ACE FOREIGN KEY (diet_id) REFERENCES diet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fridge ADD expire_date DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fridge DROP expire_date');
        $this->addSql('ALTER TABLE diet_member DROP FOREIGN KEY FK_8C2DE2E8E1E13ACE');
        $this->addSql('ALTER TABLE diet_member DROP FOREIGN KEY FK_8C2DE2E87597D3FE');
        $this->addSql('ALTER TABLE diet_member ADD CONSTRAINT FK_8C2DE2E8E1E13ACE FOREIGN KEY (diet_id) REFERENCES diet (id)');
        $this->addSql('ALTER TABLE diet_member ADD CONSTRAINT FK_8C2DE2E87597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE allergen_member DROP FOREIGN KEY FK_FCDDBB896E775A4A');
        $this->addSql('ALTER TABLE allergen_member DROP FOREIGN KEY FK_FCDDBB897597D3FE');
        $this->addSql('ALTER TABLE allergen_member ADD CONSTRAINT FK_FCDDBB896E775A4A FOREIGN KEY (allergen_id) REFERENCES allergen (id)');
        $this->addSql('ALTER TABLE allergen_member ADD CONSTRAINT FK_FCDDBB897597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE diet_recipe DROP FOREIGN KEY FK_2641A9A7E1E13ACE');
        $this->addSql('ALTER TABLE diet_recipe DROP FOREIGN KEY FK_2641A9A759D8A214');
        $this->addSql('ALTER TABLE diet_recipe ADD CONSTRAINT FK_2641A9A7E1E13ACE FOREIGN KEY (diet_id) REFERENCES diet (id)');
        $this->addSql('ALTER TABLE diet_recipe ADD CONSTRAINT FK_2641A9A759D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE allergen_recipe DROP FOREIGN KEY FK_56B1F0C66E775A4A');
        $this->addSql('ALTER TABLE allergen_recipe DROP FOREIGN KEY FK_56B1F0C659D8A214');
        $this->addSql('ALTER TABLE allergen_recipe ADD CONSTRAINT FK_56B1F0C66E775A4A FOREIGN KEY (allergen_id) REFERENCES allergen (id)');
        $this->addSql('ALTER TABLE allergen_recipe ADD CONSTRAINT FK_56B1F0C659D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
    }
}
