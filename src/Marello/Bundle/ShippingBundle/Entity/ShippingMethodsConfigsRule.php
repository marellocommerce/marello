<?php

namespace Marello\Bundle\ShippingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Marello\Bundle\RuleBundle\Entity\RuleInterface;
use Marello\Bundle\RuleBundle\Entity\RuleOwnerInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

#[ORM\Table(name: 'marello_ship_method_conf_rule')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\ShippingBundle\Entity\Repository\ShippingMethodsConfigsRuleRepository::class)]
#[Oro\Config(routeName: 'marello_shipping_methods_configs_rule_index', routeView: 'marello_shipping_methods_configs_rule_view', routeCreate: 'marello_shipping_methods_configs_rule_create', routeUpdate: 'marello_shipping_methods_configs_rule_update', defaultValues: ['ownership' => ['owner_type' => 'ORGANIZATION', 'owner_field_name' => 'organization', 'owner_column_name' => 'organization_id'], 'dataaudit' => ['auditable' => true], 'security' => ['type' => 'ACL', 'group_name' => '']])]
class ShippingMethodsConfigsRule implements RuleOwnerInterface, ExtendEntityInterface
{
    use ExtendEntityTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    private $id;

    /**
     * @var RuleInterface
     */
    #[ORM\JoinColumn(name: 'rule_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\RuleBundle\Entity\Rule::class, cascade: ['persist', 'remove'])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 10]])]
    private $rule;

    /**
     * @var Collection|ShippingMethodConfig[]
     */
    #[ORM\OneToMany(targetEntity: \Marello\Bundle\ShippingBundle\Entity\ShippingMethodConfig::class, mappedBy: 'methodConfigsRule', cascade: ['ALL'], fetch: 'EAGER', orphanRemoval: true)]
    private $methodConfigs;

    /**
     * @var Collection|ShippingMethodsConfigsRuleDestination[]
     */
    #[ORM\OneToMany(targetEntity: \Marello\Bundle\ShippingBundle\Entity\ShippingMethodsConfigsRuleDestination::class, mappedBy: 'methodConfigsRule', cascade: ['ALL'], fetch: 'EAGER', orphanRemoval: true)]
    private $destinations;

    /**
     * @var string
     */
    #[ORM\Column(name: 'currency', type: Types::STRING, length: 3, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 20]])]
    private $currency;

    /**
     * @var Organization
     */
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \Oro\Bundle\OrganizationBundle\Entity\Organization::class)]
    private $organization;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->destinations = new ArrayCollection();
        $this->methodConfigs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return RuleInterface
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
    public function setRule($rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * @param ShippingMethodConfig $lineItem
     * @return bool
     */
    public function hasMethodConfig(ShippingMethodConfig $lineItem)
    {
        return $this->methodConfigs->contains($lineItem);
    }

    /**
     * @param ShippingMethodConfig $configuration
     * @return $this
     */
    public function addMethodConfig(ShippingMethodConfig $configuration)
    {
        if (!$this->hasMethodConfig($configuration)) {
            $this->methodConfigs[] = $configuration;
            $configuration->setMethodConfigsRule($this);
        }

        return $this;
    }

    /**
     * @param ShippingMethodConfig $configuration
     * @return $this
     */
    public function removeMethodConfig(ShippingMethodConfig $configuration)
    {
        if ($this->hasMethodConfig($configuration)) {
            $this->methodConfigs->removeElement($configuration);
        }

        return $this;
    }

    /**
     * @return Collection|ShippingMethodConfig[]
     */
    public function getMethodConfigs()
    {
        return $this->methodConfigs;
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
     * @return Collection|ShippingMethodsConfigsRuleDestination[]
     */
    public function getDestinations()
    {
        return $this->destinations;
    }

    /**
     * @param ShippingMethodsConfigsRuleDestination $destination
     *
     * @return $this
     */
    public function addDestination(ShippingMethodsConfigsRuleDestination $destination)
    {
        if (!$this->destinations->contains($destination)) {
            $this->destinations->add($destination);
            $destination->setMethodConfigsRule($this);
        }

        return $this;
    }

    /**
     * @param ShippingMethodsConfigsRuleDestination $destination
     *
     * @return $this
     */
    public function removeDestination(ShippingMethodsConfigsRuleDestination $destination)
    {
        if ($this->destinations->contains($destination)) {
            $this->destinations->removeElement($destination);
        }

        return $this;
    }

    /**
     * @param Organization $organization
     *
     * @return $this
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
