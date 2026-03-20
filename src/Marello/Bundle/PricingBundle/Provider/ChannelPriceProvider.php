<?php

namespace Marello\Bundle\PricingBundle\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\PricingBundle\Entity\BasePrice;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;
use Marello\Bundle\PricingBundle\Entity\ProductChannelPrice;
use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Marello\Bundle\LayoutBundle\Context\FormChangeContextInterface;
use Marello\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Marello\Bundle\OrderBundle\Provider\OrderItem\AbstractOrderItemFormChangesProvider;

class ChannelPriceProvider extends AbstractOrderItemFormChangesProvider
{
    /** @var RoundingServiceInterface $rounding */
    protected $rounding;

    public function __construct(
        protected ManagerRegistry $registry,
        protected AclHelper $aclHelper
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function processFormChanges(FormChangeContextInterface $context)
    {
        $submittedData = $context->getSubmittedData();
        $form = $context->getForm();
        $order = $form->getData();
        if ($order instanceof Order) {
            $salesChannel = $order->getSalesChannel();
        } else {
            return;
        }
        $productIds = [];
        $data = [];
        foreach ($submittedData[self::ITEMS_FIELD] as $rowId => $item) {
            $products = $this->getProductRepository()->findBySalesChannel(
                $salesChannel->getId(),
                [(int)$item['product']],
                $this->aclHelper
            );
            $rowIdentifier = $this->getRowIdentifier($rowId, $item['product']);
            foreach ($products as $product) {
                $prices = $this->getProductPrice($salesChannel, $product, $order->getCustomer()->getCompany());
                $data['price'] = 0;
                if (array_key_exists($product->getSku(), $prices)) {
                    if (count($prices[$product->getSku()]) === 1) {
                        $prices[$product->getSku()] = $prices[$product->getSku()][0];
                    }
                    $price = $prices[$product->getSku()]['sales'];
                    if (isset($prices[$product->getSku()]['special'])) {
                        // check for the date of the special price
                        $price = $prices[$product->getSku()]['special'];
                    }
                }
                // if the provider is the advanced provider use msrp.
                // maybe we need to fix this with a setting which data to use
//                if ($priceProvider->getIdentifier() !== CompanyPriceProvider::PROVIDER_IDENTIFIER) {
//                    $price = $prices[$product->getSku()]['msrp'];
//                }
                if ($product->getId() == $item['product']) {
                    $data[$rowIdentifier]['value'] = $price;
                }
            }
                foreach ($order->getItems() as &$orderItem) {
                    if ($orderItem->getProduct()) {
                        if (isset($data[$rowIdentifier])) {
                            $orderItem->setPrice($data[$rowIdentifier]['value']);
                        }
                    }
            }
        }

        $result = $context->getResult();
        $result[self::ITEMS_FIELD]['price'] = $data;
        $context->setResult($result);
    }

    /**
     * Get channel price
     * @param SalesChannel $channel
     * @param Product $product
     * @return array $data
     */
    public function getChannelPrice($channel, $product, $order)
    {
        $data = ['hasPrice' => false];
        /** @var AssembledChannelPriceList $assembledChannelPriceList */
//        $assembledChannelPriceList = $this->getAssembledChannelPriceListRepository()->findOneBy(
//            [
//                'channel' => $channel->getId(),
//                'product' => $product->getId(),
//                'currency' => $channel->getCurrency()
//            ]
//        );
        $priceProvider = $this->getPriceProviderFromRegistry($channel);
        if ($priceProvider) {
            $prices = $this->getProductPrice($channel, $product, $order->getCustomer()->getCompany());
            $data['price'] = 0;
            if (!array_key_exists($product->getSku(), $prices)) {
                return $data;
            }

            if (count($prices[$product->getSku()]) === 1) {
                $prices[$product->getSku()] = $prices[$product->getSku()][0];
            }

            $data['price'] = $prices[$product->getSku()]['sales'];
            if (isset($prices[$product->getSku()]['special'])) {
                // check for the date of the special price
                $data['price'] = $prices[$product->getSku()]['special'];
                $data['hasPrice'] = true;
            }
//
//            /** @var ProductChannelPrice $price */
//            $dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
//            $price = $assembledChannelPriceList->getSpecialPrice()
//                    && $assembledChannelPriceList->getSpecialPrice()->isDateAvailable($dateTime)
//                ? $assembledChannelPriceList->getSpecialPrice()
//                : $assembledChannelPriceList->getDefaultPrice();
//
//            if ($price instanceof BasePrice) {
//                $data['hasPrice'] = true;
//                $data['price'] = (float)$price->getValue();
//            }
        }

        return $data;
    }

    /**
     * Get Default price by currency for product
     * @param SalesChannel $channel
     * @param Product $product
     * @return float
     */
    public function getDefaultPrice($channel, $product, $order)
    {
//        $currency = $channel->getCurrency();
//        /** @var AssembledPriceList $assembledPriceList */
//        $assembledPriceList = $this->getAssembledPriceListRepository()->findOneBy(
//            ['product' => $product->getId(), 'currency' => $currency]
//        );
//
//        if (!$assembledPriceList) {
//            return null;
//        }

        $priceProvider = $this->getPriceProviderFromRegistry($channel);
        if ($priceProvider) {
            $prices = $this->getProductPrice($channel, $product, $order->getCustomer()->getCompany());
            if (count($prices[$product->getSku()]) === 1) {
                $prices[$product->getSku()] = $prices[$product->getSku()][0];
            }

            $price = $prices[$product->getSku()]['sales'];
            if (isset($prices[$product->getSku()]['special'])) {
                // check for the date of the special price
                $price = $prices[$product->getSku()]['special'];
            }

//            $dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
//            $price = $assembledPriceList->getSpecialPrice()
//            && $assembledPriceList->getSpecialPrice()->isDateAvailable($dateTime)
//                ? $assembledPriceList->getSpecialPrice()
//                : $assembledPriceList->getDefaultPrice();
//
            return $price;
        }

        return null;
    }

    protected function getProductPrice($channel, $product, $company = null): array
    {
        $priceProvider = $this->getPriceProviderFromRegistry($channel);
        return $priceProvider->getProductPrice(
            $product,
            $channel->getCurrency(),
            $company
        );
    }

    /**
     * @return ObjectRepository|ProductRepository
     */
    protected function getProductRepository()
    {
        return $this->getRepository(Product::class);
    }

    /**
     * @return ObjectRepository
     */
    protected function getAssembledPriceListRepository()
    {
        return $this->getRepository(AssembledPriceList::class);
    }

    /**
     * @return ObjectRepository
     */
    protected function getAssembledChannelPriceListRepository()
    {
        return $this->getRepository(AssembledChannelPriceList::class);
    }

    /**
     * @param string $className
     * @return ObjectRepository
     */
    protected function getRepository($className)
    {
        return $this->registry->getManagerForClass($className)->getRepository($className);
    }

    /**
     * @param RoundingServiceInterface $roundingService
     * @return void
     */
    public function setRoundingService(RoundingServiceInterface $roundingService): void
    {
        $this->rounding = $roundingService;
    }
}
