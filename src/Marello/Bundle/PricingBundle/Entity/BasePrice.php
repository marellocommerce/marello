<?php

namespace Marello\Bundle\PricingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;

use Marello\Bundle\PricingBundle\Model\CurrencyAwareInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
class BasePrice implements CurrencyAwareInterface
{
    use EntityCreatedUpdatedAtTrait;
    use PriceDatesTrait;

    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var float
     */
    #[ORM\Column(name: 'value', type: 'money')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 40], 'dataaudit' => ['auditable' => true]])]
    protected $value;

    /**
     * @var string
     */
    #[ORM\Column(name: 'currency', type: Types::STRING, length: 3)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true], 'dataaudit' => ['auditable' => true]])]
    protected $currency;

    /**
     * @var PriceType
     */
    #[ORM\JoinColumn(name: 'type', referencedColumnName: 'name', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: PriceType::class)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true], 'dataaudit' => ['auditable' => true]])]
    protected $type;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return PriceType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param PriceType $type
     * @return $this
     */
    public function setType(PriceType $type)
    {
        $this->type = $type;
        
        return $this;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
        }
    }
}
