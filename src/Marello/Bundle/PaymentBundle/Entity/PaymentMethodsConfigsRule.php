<?php

namespace Marello\Bundle\PaymentBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;

use Marello\Bundle\RuleBundle\Entity\Rule;
use Marello\Bundle\RuleBundle\Entity\RuleInterface;
use Marello\Bundle\RuleBundle\Entity\RuleOwnerInterface;
use Marello\Bundle\PaymentBundle\Entity\Repository\PaymentMethodsConfigsRuleRepository;

#[ORM\Table(name: 'marello_payment_mtds_cfgs_rl')]
#[ORM\Entity(repositoryClass: PaymentMethodsConfigsRuleRepository::class)]
#[Oro\Config(
    routeName: 'marello_payment_methods_configs_rule_index',
    routeView: 'marello_payment_methods_configs_rule_view',
    routeCreate: 'marello_payment_methods_configs_rule_create',
    routeUpdate: 'marello_payment_methods_configs_rule_update',
    defaultValues: [
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => '']
    ]
)]
class PaymentMethodsConfigsRule implements
    RuleOwnerInterface,
    OrganizationAwareInterface,
    ExtendEntityInterface
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
     * @var Collection|PaymentMethodConfig[]
     */
    #[ORM\OneToMany(
        mappedBy: 'methodsConfigsRule',
        targetEntity: PaymentMethodConfig::class,
        cascade: ['ALL'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    protected $methodConfigs;

    /**
     * @var RuleInterface
     */
    #[ORM\JoinColumn(name: 'rule_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Rule::class, cascade: ['persist', 'remove'])]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected $rule;

    /**
     * @var Collection|PaymentMethodsConfigsRuleDestination[]
     */
    #[ORM\OneToMany(
        mappedBy: 'methodsConfigsRule',
        targetEntity: PaymentMethodsConfigsRuleDestination::class,
        cascade: ['ALL'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    protected $destinations;

    /**
     * @var string
     */
    #[ORM\Column(name: 'currency', type: Types::STRING, length: 3, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 10]])]
    protected $currency;

    /**
     * @var Organization
     */
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: Organization::class)]
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
