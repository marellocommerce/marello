<?php

namespace Marello\Bundle\PaymentBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

#[ORM\Table(name: 'marello_payment_method_config')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\PaymentBundle\Entity\Repository\PaymentMethodConfigRepository::class)]
#[Oro\Config]
class PaymentMethodConfig implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected $id;

    /**
     * @var PaymentMethodsConfigsRule
     */
    #[ORM\JoinColumn(name: 'configs_rule_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRule::class, inversedBy: 'methodConfigs')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected $methodsConfigsRule;

    /**
     * @var string
     */
    #[ORM\Column(name: 'method', type: Types::STRING, length: 255, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 10]])]
    protected $method;

    /**
     * @var array
     */
    #[ORM\Column(name: 'options', type: 'array', nullable: true)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 20]])]
    protected $options = [];

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return PaymentMethodsConfigsRule
     */
    public function getMethodsConfigsRule()
    {
        return $this->methodsConfigsRule;
    }

    /**
     * @param PaymentMethodsConfigsRule $methodsConfigsRule
     * @return $this
     */
    public function setMethodsConfigsRule($methodsConfigsRule)
    {
        $this->methodsConfigsRule = $methodsConfigsRule;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
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
}
