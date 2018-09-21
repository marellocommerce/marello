<?php

namespace Marello\Bundle\ShippingBundle\Method\Event;

use Symfony\Component\EventDispatcher\Event;

class MethodTypeRemovalEvent extends Event
{
    const NAME = 'marello_shipping.method_type_removal';

    /**
     * @var int|string
     */
    private $methodId;

    /**
     * @var int|string
     */
    private $typeId;

    /**
     * @param int|string $methodId
     * @param int|string $typeId
     */
    public function __construct($methodId, $typeId)
    {
        $this->methodId = $methodId;
        $this->typeId = $typeId;
    }

    /**
     * @return int|string
     */
    public function getMethodIdentifier()
    {
        return $this->methodId;
    }

    /**
     * @return int|string
     */
    public function getTypeIdentifier()
    {
        return $this->typeId;
    }
}
