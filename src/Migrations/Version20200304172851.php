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

final class Version20200304172851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Updates sylius_refund_payment state values to new schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE sylius_refund_payment SET state = "new" WHERE state = "New"');
        $this->addSql('UPDATE sylius_refund_payment SET state = "completed" WHERE state = "Completed"');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE sylius_refund_payment SET state = "New" WHERE state = "new"');
        $this->addSql('UPDATE sylius_refund_payment SET state = "Completed" WHERE state = "completed"');
    }
}
