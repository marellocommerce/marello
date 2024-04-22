<?php

namespace Marello\Bundle\TaxBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

/**
 * TaxRule
 *
 * @Oro\Config(
 *      routeName="marello_tax_taxrule_index",
 *      routeView="marello_tax_taxrule_view",
 *      routeUpdate="marello_tax_taxrule_update",
 *      defaultValues={
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
#[ORM\Table(name: 'marello_tax_tax_rule')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\TaxBundle\Entity\Repository\TaxRuleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TaxRule
{
    use EntityCreatedUpdatedAtTrait;

    /**
     * @var integer
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var TaxCode
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'tax_code_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\TaxBundle\Entity\TaxCode::class)]
    protected $taxCode;

    /**
     * @var TaxRate
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'tax_rate_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\TaxBundle\Entity\TaxRate::class)]
    protected $taxRate;

    /**
     * @var TaxJurisdiction
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'tax_jurisdiction_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\TaxBundle\Entity\TaxJurisdiction::class)]
    protected $taxJurisdiction;

    /**
     * @var array $data
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'data', type: 'json_array', nullable: true)]
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
