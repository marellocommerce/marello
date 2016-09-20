<?php

namespace Marello\Component\Pricing\ORM\Provider;

use Marello\Component\Pricing\Model\CurrencyAwareInterface;

interface CurrencyProviderInterface
{
    /**
     * Get currency for channel.
     * @param $channelId
     * @return array
     */
    public function getCurrencyDataByChannel($channelId);

    /**
     * Get currency symbol for currencyCode or entity.
     * @param $currencyCode
     * @return array
     */
    public function getCurrencySymbol($currencyCode);

    /**
     * Get currency data for the given entity
     * returns array in format
     * [
     *      'currencyCode'      => 'EUR',
     *      'currencySymbol'    => '€'
     * ]
     * @param CurrencyAwareInterface $entity
     * @return array
     */
    public function getCurrencyData(CurrencyAwareInterface $entity);

    /**
     * Get available currencies
     * @param $entities
     * @return array
     */
    public function getCurrencies($entities);
}
