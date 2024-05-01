<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240427230616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation ADD reclamation_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640476440D04 FOREIGN KEY (reclamation_user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_CE60640476440D04 ON reclamation (reclamation_user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492D6BA2D9');
        $this->addSql('DROP INDEX IDX_8D93D6492D6BA2D9 ON user');
        $this->addSql('ALTER TABLE user DROP reclamation_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE60640476440D04');
        $this->addSql('DROP INDEX IDX_CE60640476440D04 ON reclamation');
        $this->addSql('ALTER TABLE reclamation DROP reclamation_user_id');
        $this->addSql('ALTER TABLE `user` ADD reclamation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6492D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamation (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492D6BA2D9 ON `user` (reclamation_id)');
    }
}
