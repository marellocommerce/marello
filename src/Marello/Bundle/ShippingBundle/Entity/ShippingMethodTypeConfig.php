<?php

namespace Marello\Bundle\ShippingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

#[ORM\Table(name: 'marello_ship_method_type_conf')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\ShippingBundle\Entity\Repository\ShippingMethodTypeConfigRepository::class)]
#[Oro\Config]
class ShippingMethodTypeConfig implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'type', type: Types::STRING, length: 255, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 10]])]
    protected $type;

    /**
     * @var array
     */
    #[ORM\Column(name: 'options', type: 'array')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 20]])]
    protected $options = [];

    /**
     * @var bool
     */
    #[ORM\Column(name: 'enabled', type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 30]])]
    protected $enabled = false;

    /**
     * @var ShippingMethodConfig
     */
    #[ORM\JoinColumn(name: 'method_config_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\ShippingBundle\Entity\ShippingMethodConfig::class, inversedBy: 'typeConfigs')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected $methodConfig;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options ?: [];
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return ShippingMethodConfig
     */
    public function getMethodConfig()
    {
        return $this->methodConfig;
    }

    /**
     * @param ShippingMethodConfig $methodConfig
     * @return $this
     */
    public function setMethodConfig(ShippingMethodConfig $methodConfig)
    {
        $this->methodConfig = $methodConfig;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }
}
