<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Strategy;

use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Marello\Bundle\PricingBundle\Entity\PriceListInterface;
use Marello\Bundle\PricingBundle\Entity\PriceType;
use Marello\Bundle\PricingBundle\Model\PriceTypeInterface;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;

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
        /** @var AssembledChannelPriceList $entity */
        $result = $this->processProduct($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processCurrency($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processChannel($entity);
        if (!$result) {
            return null;
        }

        $this->processPrices($entity);

        return $entity;
    }

    protected function processChannel(AssembledChannelPriceList $entity): ?AssembledChannelPriceList
    {
        $channel = $entity->getChannel();
        if ($channel) {
            $existingChannel = $this->findEntityByIdentityValues(
                SalesChannel::class,
                ['code' => $channel->getCode()]
            );
            if ($existingChannel instanceof SalesChannel) {
                $entity->setChannel($existingChannel);
            } else {
                $this->processValidationErrors(
                    $entity,
                    [
                        $this->translator->trans('marello.product.messages.import.error.channel.invalid')
                    ]
                );

                return null;
            }
        } else {
            $this->processValidationErrors(
                $entity,
                [
                    $this->translator->trans('marello.product.messages.import.error.channel.required')
                ]
            );

            return null;
        }

        return $entity;
    }

    private function processPrices(AssembledChannelPriceList $entity): ?AssembledChannelPriceList
    {
        if ($defaultPrice = $entity->getDefaultPrice()) {
            $result = $this->processPrice($defaultPrice, $entity, PriceTypeInterface::DEFAULT_PRICE);
            if (!$result) {
                return null;
            }
            $defaultPrice->setChannel($entity->getChannel());
        }

        if ($specialPrice = $entity->getSpecialPrice()) {
            $result = $this->processPrice($specialPrice, $entity, PriceTypeInterface::SPECIAL_PRICE);
            if (!$result) {
                return null;
            }
            $specialPrice->setChannel($entity->getChannel());
        }

        return $entity;
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
