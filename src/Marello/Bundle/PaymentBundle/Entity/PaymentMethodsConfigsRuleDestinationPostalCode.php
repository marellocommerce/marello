<?php

namespace Marello\Bundle\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

/**
 * @Config(
 *     mode="hidden",
 * )
 */
#[ORM\Table(name: 'marello_pmnt_mtdscfgsrl_dst_pc')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class PaymentMethodsConfigsRuleDestinationPostalCode implements ExtendEntityInterface
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
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     *
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "identity"=true,
     *              "order"=10
     *          }
     *      }
     * )
     */
    #[ORM\Column(type: 'text', nullable: false)]
    protected $name;

    /**
     * @var PaymentMethodsConfigsRuleDestination
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'destination_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRuleDestination::class, inversedBy: 'postalCodes')]
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
