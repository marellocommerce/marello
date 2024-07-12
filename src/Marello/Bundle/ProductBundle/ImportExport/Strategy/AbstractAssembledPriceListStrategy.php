<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Strategy;

use Marello\Bundle\PricingBundle\Entity\PriceListInterface;
use Marello\Bundle\PricingBundle\Entity\PriceType;
use Marello\Bundle\PricingBundle\Entity\ProductChannelPrice;
use Marello\Bundle\PricingBundle\Entity\ProductPrice;
use Marello\Bundle\PricingBundle\Model\PriceTypeInterface;
use Oro\Bundle\ImportExportBundle\Strategy\Import\ConfigurableAddOrReplaceStrategy;

abstract class AbstractAssembledPriceListStrategy extends ConfigurableAddOrReplaceStrategy
{
    protected function processProduct(PriceListInterface $entity): ?PriceListInterface
    {
        $product = $entity->getProduct();
        if (!$product) {
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

    protected function processPrice(
        ProductPrice|ProductChannelPrice $price,
        PriceListInterface $priceList,
        string $priceTypeName
    ): ProductPrice|ProductChannelPrice {
        /** @var PriceType $priceType */
        $priceType = $this->findEntityByIdentityValues(PriceType::class, ['name' => $priceTypeName]);
        /** @var ProductPrice|ProductChannelPrice $existingPrice */
        $existingPrice = $this->findEntityByIdentityValues(
            get_class($price),
            $this->getExistingPriceConditions($priceList, $priceType)
        );
        if ($existingPrice) {
            $existingPrice->setValue($price->getValue());
            if ($priceTypeName === PriceTypeInterface::SPECIAL_PRICE) {
                $existingPrice->setStartDate($price->getStartDate());
                $existingPrice->setEndDate($price->getEndDate());
            }
            $setter = match ($priceTypeName) {
                PriceTypeInterface::SPECIAL_PRICE => 'setSpecialPrice',
                PriceTypeInterface::MSRP_PRICE => 'setMsrpPrice',
                default => 'setDefaultPrice',
            };
            $priceList->$setter($existingPrice);

            return $existingPrice;
        }

        $price->setType($priceType);

        return $price;
    }

    abstract protected function getExistingPriceConditions(PriceListInterface $priceList, PriceType $priceType): array;
}
