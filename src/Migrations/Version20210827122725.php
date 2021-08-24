<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210827122725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Tavy315 Sylius Product Subscriptions Plugin';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tavy315_sylius_product_subscriptions (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, product_id INT NOT NULL, channel_id INT NOT NULL, status INT NOT NULL, local_code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1F046E389395C3F3 (customer_id), INDEX IDX_1F046E38A80EF684 (product_id), INDEX IDX_1F046E3872F5A1AA (channel_id), INDEX IDX_CA49717C7B00651C ON tavy315_sylius_product_subscriptions (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tavy315_sylius_product_subscriptions ADD CONSTRAINT FK_1F046E389395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id)');
        $this->addSql('ALTER TABLE tavy315_sylius_product_subscriptions ADD CONSTRAINT FK_1F046E38A80EF684 FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
        $this->addSql('ALTER TABLE tavy315_sylius_product_subscriptions ADD CONSTRAINT FK_1F046E3872F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE tavy315_sylius_product_subscriptions');
    }
}
