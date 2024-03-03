<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Strategy;

use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;
use Marello\Bundle\PricingBundle\Entity\PriceListInterface;
use Marello\Bundle\PricingBundle\Entity\PriceType;
use Marello\Bundle\PricingBundle\Model\PriceTypeInterface;

class AssembledPriceListStrategy extends AbstractAssembledPriceListStrategy
{
    protected function processEntity(
        $entity,
        $isFullData = false,
        $isPersistNew = false,
        $itemData = null,
        array $searchContext = [],
        $entityIsRelation = false
    ) {
        /** @var AssembledPriceList $entity */
        $result = $this->processProduct($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processCurrency($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processPrices($entity);
        if (!$result) {
            return null;
        }

        return $entity;
    }

    private function processPrices(AssembledPriceList $entity): ?AssembledPriceList
    {
        if ($defaultPrice = $entity->getDefaultPrice()) {
            $result = $this->processPrice($defaultPrice, $entity, PriceTypeInterface::DEFAULT_PRICE);
            if (!$result) {
                return null;
            }
        }

        if ($specialPrice = $entity->getSpecialPrice()) {
            $result = $this->processPrice($specialPrice, $entity, PriceTypeInterface::SPECIAL_PRICE);
            if (!$result) {
                return null;
            }
        }

        if ($msrpPrice = $entity->getMsrpPrice()) {
            $result = $this->processPrice($msrpPrice, $entity, PriceTypeInterface::MSRP_PRICE);
            if (!$result) {
                return null;
            }
        }

        return $entity;
    }

    protected function getExistingPriceConditions(PriceListInterface $priceList, PriceType $priceType): array
    {
        return ['product' => $priceList->getProduct(), 'currency' => $priceList->getCurrency(), 'type' => $priceType];
    }
}
