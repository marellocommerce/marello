<?php

namespace Marello\Bundle\SalesBundle\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;

use Marello\Bundle\SalesBundle\Entity\Repository\SalesChannelTypeRepository;

/**
 * Class SalesChannelType
 * @package Marello\Bundle\SalesBundle\Entity
 */
#[ORM\Entity(SalesChannelTypeRepository::class)]
#[ORM\Table(name: 'marello_sales_channel_type')]
#[Oro\Config(
    defaultValues: [
        'grouping' => [
            'groups' => ['dictionary']
        ]
    ]
)]
class SalesChannelType
{
    #[ORM\Id]
    #[ORM\Column(name: 'name', type: Types::STRING, length: 64)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['identity' => true]])]
    protected ?string $name = null;

    #[ORM\Column(name: 'label', type: Types::STRING, length: 255, unique: true, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true]]
    )]
    protected ?string $label = null;

    /**
     * @param string $name
     */
    public function __construct(string $name = null)
    {
        $this->name = $name;
    }

    /**
     * Get type name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get type name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set warehouse type label
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get service type label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->label;
    }
}
