<?php

namespace Marello\Bundle\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait HasShipmentTrait
{
    #[ORM\OneToOne(targetEntity: Shipment::class)]
    protected $shipment;

    /**
     * @return Shipment|null
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * @param null|Shipment $shipment
     *
     * @return $this
     */
    public function setShipment(Shipment $shipment = null)
    {
        $this->shipment = $shipment;

        return $this;
    }
}
