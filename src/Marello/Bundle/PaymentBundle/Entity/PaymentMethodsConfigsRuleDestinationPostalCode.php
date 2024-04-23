<?php

namespace Marello\Bundle\PaymentBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

#[ORM\Table(name: 'marello_pmnt_mtdscfgsrl_dst_pc')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(mode: 'hidden')]
class PaymentMethodsConfigsRuleDestinationPostalCode implements ExtendEntityInterface
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
     * @var string
     */
    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['identity' => true, 'order' => 10]])]
    protected $name;

    /**
     * @var PaymentMethodsConfigsRuleDestination
     */
    #[ORM\JoinColumn(name: 'destination_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRuleDestination::class, inversedBy: 'postalCodes')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected $destination;

    /**
     * @return integer
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
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return PaymentMethodsConfigsRuleDestination
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param PaymentMethodsConfigsRuleDestination $destination
     * @return $this
     */
    public function setDestination(PaymentMethodsConfigsRuleDestination $destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return (string)$this->getName();
    }
}
