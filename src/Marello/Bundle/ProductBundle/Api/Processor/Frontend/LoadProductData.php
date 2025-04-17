<?php

namespace Marello\Bundle\ProductBundle\Api\Processor\Frontend;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityConfigBundle\Manager\AttributeManager;
use Oro\Bundle\ApiBundle\Processor\CustomizeLoadedData\CustomizeLoadedDataContext;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Provider\AvailableInventoryProvider;

/**
 * Load FrontendProduct Data which is an extension of the default Product.
 * This however, is only used for the frontend where all data is already combined into a single
 * result field.
 */
class LoadProductData implements ProcessorInterface
{
    public function __construct(
        protected ConfigManager $configManager,
        protected DoctrineHelper $doctrineHelper,
        protected RequestStack $requestStack,
        protected AttributeManager $attributeManager,
        protected PropertyAccessorInterface $propertyAccessor,
        protected AvailableInventoryProvider $availableInventoryProvider
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        /** @var CustomizeLoadedDataContext $context */
        $data = $context->getResult();
        if (!$context->isFieldRequested('frontendData', $data)) {
            return;
        }

        $productIdFieldName = $context->getResultFieldName('id');
        if (!$productIdFieldName || empty($data[$productIdFieldName])) {
            return;
        }

        $em = $this->doctrineHelper->getEntityManagerForClass(Product::class);
        $em->clear(Product::class);
        $request = $this->requestStack->getCurrentRequest();
        $queryFilters = $request->get('filter');
        if (!isset($queryFilters['organization'])) {
            $data['frontendData'] = [
                'error' => 'cannot fetch product(s) without organization filter'
            ];
            $context->setData($data);
            return;
        }

        if (!isset($queryFilters['saleschannels'])) {
            $data['frontendData'] = [
                'error' => 'cannot fetch product(s) without saleschannels filter'
            ];
            $context->setData($data);
            return;
        }

        $product = $em->find(Product::class, $data[$productIdFieldName]);
        if (!$product) {
            $data['frontendData'] = [
                'error' => 'Product not found'
            ];
            $context->setData($data);
            return;
        }

        $data['frontendData'] = [
            'name' => $product->getDenormalizedDefaultName(),
            'attributeFamily' => $product->getAttributeFamily()->getCode(),
            'organization' => $product->getOrganization()->getId(),
            'channels' => $this->getChannels($product),
            'categories' => $this->getCategories($product),
            'status' => $product->getStatus()->getName(),
            'taxcode' => $product->getTaxCode()->getCode(),
            'prices' => $this->getPrices($product),
            'image' => [
                'media_url' => $product->getImage()->getMediaUrl()
            ],
            'attributes' => $this->getProductAttributes($product),
            'inventoryData' => $this->getInventoryData($product, $queryFilters['saleschannels']),
            'variantAttributes' => $this->getVariantAttributes($product)
        ];

        if (str_contains($request->get('include'), 'variants')) {
            $data['frontendData']['variantData'] = $this->getVariantData($product, $queryFilters['saleschannels']);
        }

        if (str_contains($request->get('include'), 'suppliers')) {
            $data['frontendData']['suppliers'] = $this->getSuppliers($product);
        }

        $context->setData($data);
    }

    protected function getProductAttributes(Product $product)
    {
        $defaultAttributes = [
            'warranty',
            'weight',
            'barcode',
            'manufacturingCode'
        ];
        $allAttributes = [];
        $attributes = $this->attributeManager->getAttributesByFamily($product->getAttributeFamily());
        foreach ($attributes as $attribute) {
            $label = $this->attributeManager->getAttributeLabel($attribute);
            $value = $this->propertyAccessor->getValue($product, $attribute->getFieldName());
            if (in_array($attribute->getFieldName(), $defaultAttributes)) {
                $allAttributes[] = ['name' => $label, 'value' => $value];
            } else {
                $attributeScopedConfig = $attribute->toArray('frontend');
                if (isset($attributeScopedConfig['is_displayable']) && $attributeScopedConfig['is_displayable']) {
                    $allAttributes[] = ['name' => $label, 'value' => $value];
                }
            }
        }

        return $allAttributes;
    }

    protected function getSuppliers(Product $product)
    {
        $suppliers = [];
        if ($product->hasSuppliers()) {
            foreach ($product->getSuppliers() as $supplierRelation) {
                $suppliers = [
                    'code' => $supplierRelation->getSupplier()->getCode(),
                    'name' => $supplierRelation->getSupplier()->getName()
                ];
            }
        }

        return $suppliers;
    }

    protected function getInventoryData(Product $product, string $salesChannelCode)
    {
        $inventoryData = [];
        if ($inventoryItem = $product->getInventoryItem()) {
            /** @var SalesChannel $salesChannel */
            $salesChannel = $this->doctrineHelper
                ->getEntityRepositoryForClass(SalesChannel::class)
                ->findOneBy(['code' => $salesChannelCode]);
            if ($salesChannel) {
                $inventoryQty = $this->availableInventoryProvider->getAvailableInventory($product, $salesChannel);
            }
            $inventoryData = [
                'qty' => $inventoryQty ?? 0,
                'productUnit' => $inventoryItem->getProductUnit()->getName(),
                'backorderAllowed' => $inventoryItem->isBackorderAllowed(),
                'canPreOrder' => $inventoryItem->isCanPreorder(),
                'preOrderDateTime' => $inventoryItem->getPreOrdersDatetime(),
                'onDemandAllowed' => $inventoryItem->isOrderOnDemandAllowed(),
                'promises' => $this->getInventoryPromiseData($inventoryItem)
            ];
        }

        return $inventoryData;
    }

    protected function getInventoryPromiseData(InventoryItem $inventoryItem)
    {
        $promises = [];
        if ($inventoryItem->getOnHandPromise()) {
            $promises[] = [
                'type' => 'onHandPromise',
                'code' => $inventoryItem->getOnHandPromise()->getCode(),
                'label' => $inventoryItem->getOnHandPromise()->getDenormalizedDefaultLabel(),
                'minDays' => $inventoryItem->getOnHandPromise()->getMinDays(),
                'maxDays' => $inventoryItem->getOnHandPromise()->getMaxDays()
            ];
        }

        if ($inventoryItem->getBackOrderPromise()) {
            $promises[] = [
                'type' => 'backOrderPromise',
                'code' => $inventoryItem->getBackOrderPromise()->getCode(),
                'label' => $inventoryItem->getBackOrderPromise()->getDenormalizedDefaultLabel(),
                'minDays' => $inventoryItem->getBackOrderPromise()->getMinDays(),
                'maxDays' => $inventoryItem->getBackOrderPromise()->getMaxDays()
            ];
        }

        if ($inventoryItem->getPreOrderPromise()) {
            $promises[] = [
                'type' => 'preOrderPromise',
                'code' => $inventoryItem->getPreOrderPromise()->getCode(),
                'label' => $inventoryItem->getPreOrderPromise()->getDenormalizedDefaultLabel(),
                'minDays' => $inventoryItem->getPreOrderPromise()->getMinDays(),
                'maxDays' => $inventoryItem->getPreOrderPromise()->getMaxDays()
            ];
        }

        if ($inventoryItem->getOrderOnDemandPromise()) {
            $promises[] = [
                'type' => 'orderOnDemandPromise',
                'code' => $inventoryItem->getOrderOnDemandPromise()->getCode(),
                'label' => $inventoryItem->getOrderOnDemandPromise()->getDenormalizedDefaultLabel(),
                'minDays' => $inventoryItem->getOrderOnDemandPromise()->getMinDays(),
                'maxDays' => $inventoryItem->getOrderOnDemandPromise()->getMaxDays()
            ];
        }

        if ($inventoryItem->getDropShipPromise()) {
            $promises[] = [
                'type' => 'dropShipPromise',
                'code' => $inventoryItem->getDropShipPromise()->getCode(),
                'label' => $inventoryItem->getDropShipPromise()->getDenormalizedDefaultLabel(),
                'minDays' => $inventoryItem->getDropShipPromise()->getMinDays(),
                'maxDays' => $inventoryItem->getDropShipPromise()->getMaxDays()
            ];
        }

        return $promises;
    }

    protected function getVariantAttributes(Product $product)
    {
        $variantAttributes = [];
        if ($product->getVariant()) {
            foreach ($product->getVariant()->getVariantFields() as $variantField) {
                $attribute = $this->attributeManager
                    ->getAttributeByFamilyAndName($product->getAttributeFamily(), $variantField);
                $label = $this->attributeManager->getAttributeLabel($attribute);
                $function = 'get' . ucwords($label);
                if ($attribute->getType() === 'enum') {
                    $variantAttributes[] = ['name' => $label, 'value' => $product->$function()->getName()];
                } else {
                    $variantAttributes[] = ['name' => $label, 'value' => $product->$function()];
                }
            }
        }

        return $variantAttributes;
    }

    protected function getVariantData(Product $product, $salesChannelCode)
    {
        $variantData = [];
        if ($product->getVariant()) {
            $variantData = [
                'variantCode' => $product->getVariant()->getVariantCode(),
                'variantName' => $product->getVariant()->getDenormalizedDefaultName(),
                'variantImage' => [
                    'media_url' => $product->getVariant()->getImage()?->getMediaUrl()
                ],
                'variants' => []
            ];
            foreach ($product->getVariant()->getProducts() as $variantProduct) {
                $variantFields = [];
                $variantFields['name'] = $variantProduct->getDenormalizedDefaultName();
                $variantFields['sku'] = $variantProduct->getSku();
                $variantFields['prices'] = $this->getPrices($variantProduct);
                $variantFields['inventoryData'] = $this->getInventoryData($variantProduct, $salesChannelCode);
                foreach ($product->getVariant()->getVariantFields() as $variantField) {
                    $attribute = $this->attributeManager
                        ->getAttributeByFamilyAndName($variantProduct->getAttributeFamily(), $variantField);
                    $label = $this->attributeManager->getAttributeLabel($attribute);
                    $function = 'get' . ucwords($label);
                    if ($attribute->getType() === 'enum') {
                        $variantFields['attributes'][] = ['name' => $label, 'value' => $variantProduct->$function()->getName()];
                    } else {
                        $variantFields['attributes'][] = ['name' => $label, 'value' => $variantProduct->$function()];
                    }
                }
                $variantData['variants'][] = $variantFields;
            }
        }

        return $variantData;
    }

    protected function getCategories(Product $product)
    {
        $categories = [];
        foreach($product->getCategories() as $category) {
            $categories[] = ['name' => $category->getName(), 'code' => $category->getCode()];
        }
        return $categories;
    }

    protected function getChannels(Product $product)
    {
        $channels = [];
        foreach($product->getChannels() as $channel) {
            $channels[] = [
                'name' => $channel->getName(),
                'code' => $channel->getCode(),
                'currency' => $channel->getCurrency(),
                'channelType' => $channel->getChannelType()->getName()
            ];
            if ($channel->getAssociatedSalesChannel()) {
                $channels['associated'] = [
                    'code' => $channel->getAssociatedSalesChannel()->getCode(),
                    'name' => $channel->getAssociatedSalesChannel()->getName(),
                    'currency' => $channel->getAssociatedSalesChannel()->getCurrency()
                ];
            }
        }
        return $channels;
    }

    protected function getPrices(Product $product)
    {
        $prices = [];
        foreach($product->getPrices() as $priceList) {
            $prices = [
                'default' => $priceList->getDefaultPrice()?->getValue(),
                'special' => $priceList->getSpecialPrice()?->getValue(),
                'special_from' => $priceList->getSpecialPrice()?->getStartDate(),
                'special_to' => $priceList->getSpecialPrice()?->getEndDate(),
                'msrp' => $priceList->getMsrpPrice()?->getValue(),
                'currency' => $priceList->getCurrency()
            ];
        }
        return $prices;
    }
}
