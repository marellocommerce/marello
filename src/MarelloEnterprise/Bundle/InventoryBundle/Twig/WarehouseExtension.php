<?php

namespace MarelloEnterprise\Bundle\InventoryBundle\Twig;

use MarelloEnterprise\Bundle\InventoryBundle\Checker\IsFixedWarehouseGroupChecker;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WarehouseExtension extends AbstractExtension
{
    const NAME = 'marelloenterprise_warehouse';

    public function __construct(
        protected IsFixedWarehouseGroupChecker $isFixedWarehouseGroupChecker
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'marello_inventory_is_fixed_warehousegroup',
                [$this->isFixedWarehouseGroupChecker, 'check']
            ),
        ];
    }
}
