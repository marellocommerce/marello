<?php

namespace Marello\Bundle\ProductBundle\Api\Processor\Frontend;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\ApiBundle\Processor\CustomizeLoadedData\CustomizeLoadedDataContext;

use Marello\Bundle\ProductBundle\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;

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
        protected RequestStack $requestStack
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        /** @var CustomizeLoadedDataContext $context */
        $data = $context->getResult();
        if (!$context->isFieldRequested('frontendAttributes', $data)) {
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
//        if (!isset($queryFilters['organization'])) {
//            throw new \Exception('cannot fetch product(s) without organization filter');
//        }

        $product = $em->find(Product::class, $data[$productIdFieldName]);

        if (!$product) {
            return;
        }

        $data['frontendAttributes'][] = [
            'name' => $product->getDenormalizedDefaultName(),
            'attributeFamily' => $product->getAttributeFamily()->getCode(),
            'organization' => $product->getOrganization()->getId(),
            'channels' => $this->getChannels($product),
            'categories' => $this->getCategories($product),
            'status' => $product->getStatus()->getName(),
            'taxcode' => $product->getTaxCode()->getCode(),
            'prices' => $this->getPrices($product),
            'image' => [
                'media_url' => $product->getImage()->getMediaUrl(),
                'content' => $product->getImage()->getMediaUrl() ?? $product->getImage()
            ],
            'variantData' => $this->getVariantData($product)

        ];
//        var_dump($data);
        $context->setData($data);
    }

    protected function getVariantData(Product $product)
    {
        $variantData = [];
        if ($product->getVariant()) {
            $variantData[] = [
                'variantCode' => $product->getVariant()->getVariantCode(),
                'variantName' => $product->getVariant()->getDenormalizedDefaultName(),
                'variantDescription' => $product->getVariant()->getDescriptions()->first(),
                'variantImage' => [
                    'media_url' => $product->getVariant()->getImage()?->getMediaUrl(),
                    'content' => $product->getVariant()->getImage()?->getMediaUrl() ?? $product->getVariant()->getImage()
                ],
                'variantFields' => $product->getVariant()->getVariantFields()
            ];

            foreach($product->getVariant()->getProducts() as $variantProduct) {
                $variantData[] = ['name' => $variantProduct->getDenormalizedDefaultName(), 'sku' => $variantProduct->getSku()];
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
            $prices[] = [
                'default' => $priceList->getDefaultPrice()?->getValue(),
                'special' => $priceList->getSpecialPrice()?->getValue(),
                'special_from' => $priceList->getSpecialPrice()?->getStartDate(),
                'special_to' => $priceList->getSpecialPrice()?->getEndDate(),
                'currency' => $priceList->getCurrency()
            ];
        }
        return $prices;
    }
}
