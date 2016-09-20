<?php

namespace Marello\Component\Inventory\Repository;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;
use Marello\Component\Product\Model\ProductInterface;

interface InventoryLogRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * Returns a sequence of records containing values representing how much were respective quantities changed on each
     * day between given from and to values.
     *
     * @param ProductInterface   $product
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return array
     */
    public function getQuantitiesForProduct(ProductInterface $product, \DateTime $from, \DateTime $to);

    /**
     * Returns initial quantities for given day. Quantity values at the start of the day.
     * This is either old value of first record of the day, or new value of last record before the day.
     * In case no record is preset, bot quantities are returned as zeroes.
     *
     * @param ProductInterface   $product
     * @param \DateTime $at
     *
     * @return array
     */
    public function getInitialQuantities(ProductInterface $product, \DateTime $at);
}
