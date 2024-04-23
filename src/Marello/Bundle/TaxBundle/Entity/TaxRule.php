<?php

namespace Marello\Bundle\TaxBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;

/**
 * TaxRule
 */
#[ORM\Table(name: 'marello_tax_tax_rule')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\TaxBundle\Entity\Repository\TaxRuleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(routeName: 'marello_tax_taxrule_index', routeView: 'marello_tax_taxrule_view', routeUpdate: 'marello_tax_taxrule_update', defaultValues: ['dataaudit' => ['auditable' => true], 'security' => ['type' => 'ACL', 'group_name' => '']])]
class TaxRule
{
    use EntityCreatedUpdatedAtTrait;

    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var TaxCode
     */
    #[ORM\JoinColumn(name: 'tax_code_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\TaxBundle\Entity\TaxCode::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $taxCode;

    /**
     * @var TaxRate
     */
    #[ORM\JoinColumn(name: 'tax_rate_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\TaxBundle\Entity\TaxRate::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $taxRate;

    /**
     * @var TaxJurisdiction
     */
    #[ORM\JoinColumn(name: 'tax_jurisdiction_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\TaxBundle\Entity\TaxJurisdiction::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $taxJurisdiction;

    /**
     * @var array $data
     */
    #[ORM\Column(name: 'data', type: Types::JSON, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected $data = [];

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->taxCode. ' '. $this->taxRate;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set taxCode
     *
     * @param TaxCode $taxCode
     *
     * @return TaxRule
     */
    public function setTaxCode(TaxCode $taxCode)
    {
        $this->taxCode = $taxCode;

        return $this;
    }

    /**
     * Get taxCode
     *
     * @return TaxCode
     */
    public function getTaxCode()
    {
        return $this->taxCode;
    }

    /**
     * Set taxRate
     *
     * @param TaxRate $taxRate
     *
     * @return TaxRule
     */
    public function setTaxRate(TaxRate $taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * Get taxRate
     *
     * @return TaxRate
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @param TaxJurisdiction $taxJurisdiction
     *
     * @return $this
     */
    public function setTaxJurisdiction(TaxJurisdiction $taxJurisdiction = null)
    {
        $this->taxJurisdiction = $taxJurisdiction;

        return $this;
    }

    /**
     * @return TaxJurisdiction
     */
    public function getTaxJurisdiction()
    {
        return $this->taxJurisdiction;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
