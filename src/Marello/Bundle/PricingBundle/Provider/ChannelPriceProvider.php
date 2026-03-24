<?php

namespace Marello\Bundle\PricingBundle\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\PricingBundle\Entity\BasePrice;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;
use Marello\Bundle\PricingBundle\Entity\ProductChannelPrice;
use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Marello\Bundle\PricingBundle\DependencyInjection\Configuration;
use Marello\Bundle\LayoutBundle\Context\FormChangeContextInterface;
use Marello\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Marello\Bundle\OrderBundle\Provider\OrderItem\AbstractOrderItemFormChangesProvider;

class ChannelPriceProvider extends AbstractOrderItemFormChangesProvider
{
    /** @var RoundingServiceInterface $rounding */
    protected $rounding;

    public function __construct(
        protected ManagerRegistry $registry,
        protected AclHelper $aclHelper,
        protected ConfigManager $configManager,
        protected CompanyPriceProviderRegistry $priceProviderRegistry
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

        $data = [];
        foreach ($submittedData[self::ITEMS_FIELD] as $rowId => $item) {
            $products = $this->getProductRepository()->findBySalesChannel(
                $salesChannel->getId(),
                [(int)$item['product']],
                $this->aclHelper
            );
            $rowIdentifier = $this->getRowIdentifier($rowId, $item['product']);
            foreach ($products as $product) {
                $price = $this->getDefaultPrice($salesChannel, $product, $order);
//                if ($channelPrice = $this->getChannelPrice($salesChannel, $product, $order)) {
//                    $price = $channelPrice;
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

    public function getProductPrice($salesChannel, $product, $company = null)
    {
        $providerId = $this->configManager->get(Configuration::PRICING_PROVIDER, false, false, $salesChannel);
        $priceProvider = $this->priceProviderRegistry->getPriceProvider($providerId);
        $prices = $priceProvider->getProductPrice($product, $salesChannel->getCurrency(), $company);
        if (isset($prices[$product->getSku()])) {
            return $prices[$product->getSku()];
        }

        return [];
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
        $prices = $this->getProductPrice($channel, $product, $order->getCustomer()->getCompany());
        if (count($prices) === 1) {
            $prices[$product->getSku()] = array_shift($prices);
        } elseif (!array_key_exists($product->getSku(), $prices)) {
            $prices[$product->getSku()] = $prices;
        }

        // need to fix with dates
        if (isset($prices[$product->getSku()]['special'])) {
            // check for the date of the special price
            $data['hasPrice'] = true;
            $data['price'] = $prices[$product->getSku()]['special'];
        }

        // if the provider is the advanced provider use msrp.
        // maybe we need to fix this with a setting which data to use
//                if ($priceProvider->getIdentifier() !== CompanyPriceProvider::PROVIDER_IDENTIFIER) {
//                    $price = $prices[$product->getSku()]['msrp'];
//                }

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
        $prices = $this->getProductPrice($channel, $product, $order->getCustomer()->getCompany());
        if (count($prices) === 1) {
            $prices[$product->getSku()] = array_shift($prices);
        } elseif (!array_key_exists($product->getSku(), $prices)) {
            $prices[$product->getSku()] = $prices;
        }

        $price = $prices[$product->getSku()]['sales'];
        if (isset($prices[$product->getSku()]['special'])) {
            // check for the date of the special price
            $price = $prices[$product->getSku()]['special'];
        }
        // if the provider is the advanced provider use msrp.
        // maybe we need to fix this with a setting which data to use
//                if ($priceProvider->getIdentifier() !== CompanyPriceProvider::PROVIDER_IDENTIFIER) {
//                    $price = $prices[$product->getSku()]['msrp'];
//                }
        return $price;
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
