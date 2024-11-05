<?php

namespace Marello\Bundle\PurchaseOrderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Marello\Bundle\PurchaseOrderBundle\DependencyInjection\MarelloPurchaseOrderExtension;

class MarelloPurchaseOrderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new MarelloPurchaseOrderExtension();
    }
}
