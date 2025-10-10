<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251010121852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur_avis (utilisateur_id INT NOT NULL, avis_id INT NOT NULL, INDEX IDX_4610C7CAFB88E14F (utilisateur_id), INDEX IDX_4610C7CA197E709F (avis_id), PRIMARY KEY(utilisateur_id, avis_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur_covoiturage (utilisateur_id INT NOT NULL, covoiturage_id INT NOT NULL, INDEX IDX_DC21931AFB88E14F (utilisateur_id), INDEX IDX_DC21931A62671590 (covoiturage_id), PRIMARY KEY(utilisateur_id, covoiturage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE utilisateur_avis ADD CONSTRAINT FK_4610C7CAFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_avis ADD CONSTRAINT FK_4610C7CA197E709F FOREIGN KEY (avis_id) REFERENCES avis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_covoiturage ADD CONSTRAINT FK_DC21931AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_covoiturage ADD CONSTRAINT FK_DC21931A62671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE covoiturage ADD voiture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('CREATE INDEX IDX_28C79E89181A8BA ON covoiturage (voiture_id)');
        $this->addSql('ALTER TABLE utilisateur ADD role_id INT NOT NULL, ADD configuration_id INT NOT NULL, ADD code_postal INT NOT NULL, CHANGE photo photo VARCHAR(150) NOT NULL, CHANGE adresse ville VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B373F32DD8 FOREIGN KEY (configuration_id) REFERENCES configuration (id)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3D60322AC ON utilisateur (role_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B373F32DD8 ON utilisateur (configuration_id)');
        $this->addSql('ALTER TABLE voiture ADD utilisateur_id INT NOT NULL, ADD marque_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810F4827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('CREATE INDEX IDX_E9E2810FFB88E14F ON voiture (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_E9E2810F4827B9B2 ON voiture (marque_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur_avis DROP FOREIGN KEY FK_4610C7CAFB88E14F');
        $this->addSql('ALTER TABLE utilisateur_avis DROP FOREIGN KEY FK_4610C7CA197E709F');
        $this->addSql('ALTER TABLE utilisateur_covoiturage DROP FOREIGN KEY FK_DC21931AFB88E14F');
        $this->addSql('ALTER TABLE utilisateur_covoiturage DROP FOREIGN KEY FK_DC21931A62671590');
        $this->addSql('DROP TABLE utilisateur_avis');
        $this->addSql('DROP TABLE utilisateur_covoiturage');
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E89181A8BA');
        $this->addSql('DROP INDEX IDX_28C79E89181A8BA ON covoiturage');
        $this->addSql('ALTER TABLE covoiturage DROP voiture_id');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3D60322AC');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B373F32DD8');
        $this->addSql('DROP INDEX IDX_1D1C63B3D60322AC ON utilisateur');
        $this->addSql('DROP INDEX UNIQ_1D1C63B373F32DD8 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP role_id, DROP configuration_id, DROP code_postal, CHANGE photo photo VARCHAR(50) NOT NULL, CHANGE ville adresse VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FFB88E14F');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810F4827B9B2');
        $this->addSql('DROP INDEX IDX_E9E2810FFB88E14F ON voiture');
        $this->addSql('DROP INDEX IDX_E9E2810F4827B9B2 ON voiture');
        $this->addSql('ALTER TABLE voiture DROP utilisateur_id, DROP marque_id');
    }
}
