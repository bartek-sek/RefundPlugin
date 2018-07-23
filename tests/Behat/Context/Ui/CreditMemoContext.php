<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Tests\Sylius\RefundPlugin\Behat\Page\CreditMemoDetailsPageInterface;
use Tests\Sylius\RefundPlugin\Behat\Page\Order\ShowPageInterface;
use Webmozart\Assert\Assert;

final class CreditMemoContext implements Context
{
    /** @var ShowPageInterface */
    private $orderShowPage;

    /** @var CreditMemoDetailsPageInterface */
    private $creditMemoDetailsPage;

    /** @var ObjectRepository */
    private $creditMemoRepository;

    public function __construct(
        ShowPageInterface $orderShowPage,
        CreditMemoDetailsPageInterface $creditMemoDetailsPage,
        ObjectRepository $creditMemoRepository
    ) {
        $this->orderShowPage = $orderShowPage;
        $this->creditMemoDetailsPage = $creditMemoDetailsPage;
        $this->creditMemoRepository = $creditMemoRepository;
    }

    /**
     * @When I browse the details of the only credit memo generated for order :order
     */
    public function browseTheDetailsOfTheOnlyCreditMemoGeneratedForOrder(OrderInterface $order): void
    {
        $orderNumber = $order->getNumber();
        $creditMemo = $this->creditMemoRepository->findBy(['orderNumber' => $orderNumber])[0];

        $this->creditMemoDetailsPage->open(['orderNumber' => $orderNumber, 'id' => $creditMemo->getId()]);
    }

    /**
     * @Then I should have :count credit memo generated for order :order
     */
    public function shouldHaveCountCreditMemoGeneratedForOrder(int $count, OrderInterface $order): void
    {
        $this->orderShowPage->open(['id' => $order->getId()]);
        Assert::same($this->orderShowPage->countCreditMemos(), $count);
    }

    /**
     * @Then this credit memo should contain :count :productName product, with :discount discount and :tax tax applied
     */
    public function thisCreditMemoShouldContainProductWithDiscountAndTaxApplied(
        int $count,
        string $productName,
        int $discount,
        int $tax
    ): void {
        Assert::same($this->creditMemoDetailsPage->countUnitsWithProduct($productName), $count);
        Assert::same($this->creditMemoDetailsPage->getUnitDiscount($count, $productName), $discount);
        Assert::same($this->creditMemoDetailsPage->getUnitTax($count, $productName), $tax);
    }

    /**
     * @Then its total should be :total
     */
    public function creditMemoTotalShouldBe(string $total): void
    {
        Assert::same($this->creditMemoDetailsPage->getTotal(), $total);
    }
}
