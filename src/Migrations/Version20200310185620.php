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

final class Version20200310185620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert sylius_refund_credit_memo issued_at to datetime_immutable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_refund_credit_memo CHANGE issued_at issued_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_refund_credit_memo CHANGE issued_at issued_at DATETIME NOT NULL');
    }
}
