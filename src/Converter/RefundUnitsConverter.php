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

namespace Sylius\RefundPlugin\Converter;

use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface;
use Sylius\RefundPlugin\Model\RefundTypeInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Webmozart\Assert\Assert;

final class RefundUnitsConverter implements RefundUnitsConverterInterface
{
    public function __construct(private readonly UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator)
    {
    }

    /**
     * @param UnitRefundInterface[] $units
     *
     * @return UnitRefundInterface[]
     */
    public function convert(array $units, string|RefundTypeInterface $unitRefundClass): array
    {
        $args = func_get_args();
        $refundType = null;

        if ($unitRefundClass instanceof RefundTypeInterface) {
            $refundType = $unitRefundClass;

            if (!isset($args[2]) || !is_string($args[2])) {
                throw new \InvalidArgumentException('The refundType must be present and be a string');
            }

            $unitRefundClass = $args[2];

            trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('Passing an "%s" as a 2nd argument of "%s::convert" method is deprecated and will be removed in 2.0.', RefundTypeInterface::class, self::class));
        }

        $units = $this->filterEmptyRefundUnits($units);
        $refundUnits = [];
        foreach ($units as $id => $unit) {
            $total = $this
                ->unitRefundTotalCalculator
                ->calculateForUnitWithIdAndType(
                    $id,
                    null === $refundType ? $unitRefundClass::type() : $refundType,
                    /** @phpstan-ignore-next-line  */
                    $this->getAmount($unit),
                )
            ;

            $unitRefund = new $unitRefundClass((int) $id, $total);
            Assert::isInstanceOf($unitRefund, UnitRefundInterface::class);

            $refundUnits[] = $unitRefund;
        }

        return $refundUnits;
    }

    /**
     * @param UnitRefundInterface[] $units
     *
     * @return UnitRefundInterface[]
     */
    private function filterEmptyRefundUnits(array $units): array
    {
        /** @phpstan-ignore-next-line  */
        return array_filter($units, function (array $refundUnit): bool {
            return (isset($refundUnit['amount']) && $refundUnit['amount'] !== '') || isset($refundUnit['full']);
        });
    }

    /** @param UnitRefundInterface[] $unit */
    private function getAmount(array $unit): ?float
    {
        if (isset($unit['full'])) {
            return null;
        }

        Assert::keyExists($unit, 'amount');

        /** @phpstan-ignore-next-line  */
        return (float) $unit['amount'];
    }
}
