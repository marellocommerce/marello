<?php

namespace Marello\Bundle\ShippingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table('marello_ship_method_post_code')]
#[ORM\Entity]
class ShippingMethodsConfigsRuleDestinationPostalCode
{
    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    private $name;

    /**
     * @var ShippingMethodsConfigsRuleDestination
     */
    #[ORM\JoinColumn(name: 'destination_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: ShippingMethodsConfigsRuleDestination::class, inversedBy: 'postalCodes')]
    private $destination;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ShippingMethodsConfigsRuleDestinationPostalCode
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ShippingMethodsConfigsRuleDestination
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param ShippingMethodsConfigsRuleDestination $destination
     * @return ShippingMethodsConfigsRuleDestinationPostalCode
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
