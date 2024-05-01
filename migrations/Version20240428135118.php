<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240428135118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement (id_event INT AUTO_INCREMENT NOT NULL, nom_event VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, organisateur VARCHAR(255) NOT NULL, date_deb DATE NOT NULL, date_fin DATE NOT NULL, capacite INT NOT NULL, image VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, nb_place_res VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id_event)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, id_reser_event INT DEFAULT NULL, nom_rese_event VARCHAR(255) NOT NULL, nbr_place_reserv INT NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_42C84955C8938F48 (id_reser_event), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955C8938F48 FOREIGN KEY (id_reser_event) REFERENCES evenement (id_event)');
        $this->addSql('ALTER TABLE panier DROP id_user');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF23E940D95 FOREIGN KEY (id_prod) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_24CC0DF23E940D95 ON panier (id_prod)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27C9486A13 FOREIGN KEY (id_categorie) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27C9486A13 ON produit (id_categorie)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955C8938F48');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF23E940D95');
        $this->addSql('DROP INDEX IDX_24CC0DF23E940D95 ON panier');
        $this->addSql('ALTER TABLE panier ADD id_user INT NOT NULL');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27C9486A13');
        $this->addSql('DROP INDEX IDX_29A5EC27C9486A13 ON produit');
    }
}
