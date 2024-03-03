<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Strategy;

use Marello\Bundle\PricingBundle\Entity\PriceListInterface;
use Marello\Bundle\PricingBundle\Entity\PriceType;
use Marello\Bundle\PricingBundle\Entity\ProductChannelPrice;
use Marello\Bundle\PricingBundle\Entity\ProductPrice;
use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ImportExportBundle\Strategy\Import\ConfigurableAddOrReplaceStrategy;

abstract class AbstractAssembledPriceListStrategy extends ConfigurableAddOrReplaceStrategy
{
    protected function processProduct(PriceListInterface $entity): ?PriceListInterface
    {
        $product = $entity->getProduct();
        if ($product) {
            $existingProduct = $this->findEntityByIdentityValues(
                Product::class,
                ['sku' => $product->getSku()]
            );
            if ($existingProduct instanceof Product) {
                $entity->setProduct($existingProduct);
            } else {
                $this->processValidationErrors(
                    $entity,
                    [
                        $this->translator->trans('marello.product.messages.import.error.product.invalid')
                    ]
                );

                return null;
            }
        } else {
            $this->processValidationErrors(
                $entity,
                [
                    $this->translator->trans('marello.product.messages.import.error.product.required')
                ]
            );

            return null;
        }

        return $entity;
    }

    protected function processCurrency(PriceListInterface $entity): ?PriceListInterface
    {
        $currency = $entity->getCurrency();
        if (!$currency) {
            $this->processValidationErrors(
                $entity,
                [
                    $this->translator->trans('marello.product.messages.import.error.currency.required')
                ]
            );

            return null;
        }

        return $entity;
    }

    protected function processPrice(
        ProductPrice|ProductChannelPrice $price,
        PriceListInterface $priceList,
        string $priceTypeName
    ): ProductPrice|ProductChannelPrice|null {
        /** @var PriceType $priceType */
        $priceType = $this->findEntityByIdentityValues(
            PriceType::class,
            ['name' => $priceTypeName]
        );
        if ($this->findEntityByIdentityValues(
            get_class($price),
            $this->getExistingPriceConditions($priceList, $priceType)
        )) {
            $this->processValidationErrors(
                $priceList,
                [
                    $this->translator->trans(
                        'marello.product.messages.import.error.price.already_exists',
                        ['type' => $priceTypeName]
                    )
                ]
            );

            return null;
        }

        $price->setProduct($priceList->getProduct());
        $price->setCurrency($priceList->getCurrency());
        $price->setType($priceType);

        return $price;
    }

    abstract protected function getExistingPriceConditions(PriceListInterface $priceList, PriceType $priceType): array;
}
