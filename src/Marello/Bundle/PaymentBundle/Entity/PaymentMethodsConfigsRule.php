<?php

namespace Marello\Bundle\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Marello\Bundle\RuleBundle\Entity\RuleInterface;
use Marello\Bundle\RuleBundle\Entity\RuleOwnerInterface;

/**
 * @Config(
 *      routeName="marello_payment_methods_configs_rule_index",
 *      routeView="marello_payment_methods_configs_rule_view",
 *      routeCreate="marello_payment_methods_configs_rule_create",
 *      routeUpdate="marello_payment_methods_configs_rule_update",
 *      defaultValues={
 *          "ownership"={
 *              "owner_type"="ORGANIZATION",
 *              "owner_field_name"="organization",
 *              "owner_column_name"="organization_id"
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"=""
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_payment_mtds_cfgs_rl')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\PaymentBundle\Entity\Repository\PaymentMethodsConfigsRuleRepository::class)]
class PaymentMethodsConfigsRule implements
    RuleOwnerInterface,
    OrganizationAwareInterface,
    ExtendEntityInterface
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
     * @var Collection|PaymentMethodConfig[]
     */
    #[ORM\OneToMany(targetEntity: \Marello\Bundle\PaymentBundle\Entity\PaymentMethodConfig::class, mappedBy: 'methodsConfigsRule', cascade: ['ALL'], fetch: 'EAGER', orphanRemoval: true)]
    protected $methodConfigs;

    /**
     * @var RuleInterface
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'rule_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\RuleBundle\Entity\Rule::class, cascade: ['persist', 'remove'])]
    protected $rule;

    /**
     * @var Collection|PaymentMethodsConfigsRuleDestination[]
     */
    #[ORM\OneToMany(targetEntity: \Marello\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRuleDestination::class, mappedBy: 'methodsConfigsRule', cascade: ['ALL'], fetch: 'EAGER', orphanRemoval: true)]
    protected $destinations;

    /**
     * @var string
     *
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "order"=10
     *          }
     *      }
     *  )
     */
    #[ORM\Column(name: 'currency', type: 'string', length: 3, nullable: false)]
    protected $currency;

    /**
     * @var Organization
     */
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \Oro\Bundle\OrganizationBundle\Entity\Organization::class)]
    protected $organization;

    public function __construct()
    {
        $this->methodConfigs = new ArrayCollection();
        $this->destinations = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @param RuleInterface $rule
     *
     * @return $this
     */
    public function setRule(RuleInterface $rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|PaymentMethodConfig[]
     */
    public function getMethodConfigs()
    {
        return $this->methodConfigs;
    }

    /**
     * @param PaymentMethodConfig $methodConfig
     *
     * @return bool
     */
    public function hasMethodConfig(PaymentMethodConfig $methodConfig)
    {
        return $this->methodConfigs->contains($methodConfig);
    }

    /**
     * @param PaymentMethodConfig $methodConfig
     *
     * @return $this
     */
    public function addMethodConfig(PaymentMethodConfig $methodConfig)
    {
        if (!$this->hasMethodConfig($methodConfig)) {
            $this->methodConfigs[] = $methodConfig;
            $methodConfig->setMethodsConfigsRule($this);
        }

        return $this;
    }

    /**
     * @param PaymentMethodConfig $methodConfig
     *
     * @return $this
     */
    public function removeMethodConfig(PaymentMethodConfig $methodConfig)
    {
        if ($this->hasMethodConfig($methodConfig)) {
            $this->methodConfigs->removeElement($methodConfig);
        }

        return $this;
    }

    /**
     * @return Collection|PaymentMethodsConfigsRuleDestination[]
     */
    public function getDestinations()
    {
        return $this->destinations;
    }

    /**
     * @param PaymentMethodsConfigsRuleDestination $destination
     *
     * @return $this
     */
    public function addDestination(PaymentMethodsConfigsRuleDestination $destination)
    {
        if (!$this->destinations->contains($destination)) {
            $this->destinations->add($destination);
            $destination->setMethodsConfigsRule($this);
        }

        return $this;
    }

    /**
     * @param PaymentMethodsConfigsRuleDestination $destination
     *
     * @return $this
     */
    public function removeDestination(PaymentMethodsConfigsRuleDestination $destination)
    {
        if ($this->destinations->contains($destination)) {
            $this->destinations->removeElement($destination);
        }

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
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return OrganizationInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param OrganizationInterface $organization
     *
     * @return $this
     */
    public function setOrganization(OrganizationInterface $organization)
    {
        $this->organization = $organization;

        return $this;
    }
}
