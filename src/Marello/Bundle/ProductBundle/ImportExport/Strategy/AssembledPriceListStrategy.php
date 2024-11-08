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
        $entity = parent::processEntity($entity, $isFullData, $isPersistNew, $itemData, $searchContext);
        if (!$entity instanceof AssembledPriceList) {
            return $entity;
        }

        $result = $this->processProduct($entity);
        if (!$result) {
            return null;
        }

        $this->processPrices($entity);

        return $entity;
    }

    protected function processProduct(PriceListInterface|AssembledPriceList $entity): ?PriceListInterface
    {
        $result = parent::processProduct($entity);
        if (!$result) {
            return null;
        }

        // Check available currencies
        foreach ($entity->getProduct()->getChannels() as $salesChannel) {
            if ($salesChannel->getCurrency() === $entity->getCurrency()) {
                return $entity;
            }
        }

        $this->processValidationErrors(
            $entity,
            [
                $this->translator->trans('marello.product.messages.import.error.price_currency.invalid')
            ]
        );

        return null;
    }

    private function processPrices(AssembledPriceList $entity): void
    {
        if ($defaultPrice = $entity->getDefaultPrice()) {
            $this->processPrice($defaultPrice, $entity, PriceTypeInterface::DEFAULT_PRICE);
        }

        if ($specialPrice = $entity->getSpecialPrice()) {
            $this->processPrice($specialPrice, $entity, PriceTypeInterface::SPECIAL_PRICE);
        }

        if ($msrpPrice = $entity->getMsrpPrice()) {
            $this->processPrice($msrpPrice, $entity, PriceTypeInterface::MSRP_PRICE);
        }
    }

    protected function getExistingPriceConditions(PriceListInterface $priceList, PriceType $priceType): array
    {
        return ['product' => $priceList->getProduct(), 'currency' => $priceList->getCurrency(), 'type' => $priceType];
    }
}
