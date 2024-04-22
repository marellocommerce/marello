<?php

namespace Marello\Bundle\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

/**
 * @Config()
 */
#[ORM\Table(name: 'marello_payment_method_config')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\PaymentBundle\Entity\Repository\PaymentMethodConfigRepository::class)]
class PaymentMethodConfig implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    /**
     * @var integer
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'id')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var PaymentMethodsConfigsRule
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'configs_rule_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRule::class, inversedBy: 'methodConfigs')]
    protected $methodsConfigsRule;

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
    #[ORM\Column(name: 'method', type: 'string', length: 255, nullable: false)]
    protected $method;

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
    #[ORM\Column(name: 'options', type: 'array', nullable: true)]
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
