<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Strategy;

use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Marello\Bundle\PricingBundle\Entity\PriceListInterface;
use Marello\Bundle\PricingBundle\Entity\PriceType;
use Marello\Bundle\PricingBundle\Model\PriceTypeInterface;

class AssembledChannelPriceListStrategy extends AbstractAssembledPriceListStrategy
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
        if (!$entity instanceof AssembledChannelPriceList) {
            return $entity;
        }

        $result = $this->processChannel($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processProduct($entity);
        if (!$result) {
            return null;
        }

        $this->processPrices($entity);

        return $entity;
    }

    protected function processProduct(PriceListInterface|AssembledChannelPriceList $entity): ?PriceListInterface
    {
        $result = parent::processProduct($entity);
        if (!$result) {
            return null;
        }

        // Check available SalesChannels
        foreach ($entity->getProduct()->getChannels() as $salesChannel) {
            if ($salesChannel->getCode() === $entity->getChannel()->getCode()) {
                return $entity;
            }
        }

        $this->processValidationErrors(
            $entity,
            [
                $this->translator->trans('marello.product.messages.import.error.price_channel.invalid')
            ]
        );

        return null;
    }

    protected function processChannel(AssembledChannelPriceList $entity): ?AssembledChannelPriceList
    {
        $channel = $entity->getChannel();
        if (!$channel) {
            $this->processValidationErrors(
                $entity,
                [
                    $this->translator->trans('marello.product.messages.import.error.channel.required')
                ]
            );

            return null;
        }

        if ($channel->getCurrency() !== $entity->getCurrency()) {
            $this->processValidationErrors(
                $entity,
                [
                    $this->translator->trans('marello.product.messages.import.error.price_channel.currency.invalid')
                ]
            );

            return null;
        }

        return $entity;
    }

    private function processPrices(AssembledChannelPriceList $entity): void
    {
        if ($defaultPrice = $entity->getDefaultPrice()) {
            $this->processPrice($defaultPrice, $entity, PriceTypeInterface::DEFAULT_PRICE);
            $defaultPrice->setChannel($entity->getChannel());
        }

        if ($specialPrice = $entity->getSpecialPrice()) {
            $this->processPrice($specialPrice, $entity, PriceTypeInterface::SPECIAL_PRICE);
            $specialPrice->setChannel($entity->getChannel());
        }
    }

    protected function getExistingPriceConditions(PriceListInterface $priceList, PriceType $priceType): array
    {
        /** @var AssembledChannelPriceList $priceList */
        return [
            'product' => $priceList->getProduct(),
            'channel' => $priceList->getChannel(),
            'currency' => $priceList->getCurrency(),
            'type' => $priceType
        ];
    }
}
