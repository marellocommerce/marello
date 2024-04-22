<?php

namespace Marello\Bundle\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

/**
 * @Config
 */
#[ORM\Table(name: 'marello_ship_method_type_conf')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\ShippingBundle\Entity\Repository\ShippingMethodTypeConfigRepository::class)]
class ShippingMethodTypeConfig implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'id')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=10
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'type', type: 'string', length: 255, nullable: false)]
    protected $type;

    /**
     * @var array
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=20
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'options', type: 'array')]
    protected $options = [];

    /**
     * @var bool
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=30
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'enabled', type: 'boolean', nullable: false, options: ['default' => false])]
    protected $enabled = false;

    /**
     * @var ShippingMethodConfig
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'method_config_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\ShippingBundle\Entity\ShippingMethodConfig::class, inversedBy: 'typeConfigs')]
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
