<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20200306153205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renames sylius_refund_payment table to sylius_refund_refund_payment';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sylius_refund_payment TO sylius_refund_refund_payment');
        $this->addSql('ALTER TABLE sylius_refund_refund_payment DROP INDEX IDX_EFA5A4B25AA1164F, ADD INDEX IDX_EC283EA55AA1164F (payment_method_id)');
        $this->addSql('ALTER TABLE sylius_refund_refund_payment DROP FOREIGN KEY FK_EFA5A4B25AA1164F, ADD CONSTRAINT FK_EC283EA55AA1164F FOREIGN KEY (payment_method_id) REFERENCES sylius_payment_method (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sylius_refund_refund_payment TO sylius_refund_payment');
        $this->addSql('ALTER TABLE sylius_refund_payment DROP INDEX IDX_EC283EA55AA1164F, ADD INDEX IDX_EFA5A4B25AA1164F (payment_method_id)');
        $this->addSql('ALTER TABLE sylius_refund_payment DROP FOREIGN KEY FK_EC283EA55AA1164F, ADD CONSTRAINT FK_EFA5A4B25AA1164F FOREIGN KEY (payment_method_id) REFERENCES sylius_payment_method (id)');
    }
}
