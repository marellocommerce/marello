<?php

namespace Marello\Bundle\TaxBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\AddressBundle\Entity\Country;
use Oro\Bundle\AddressBundle\Entity\Region;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;

/**
 * @Config(
 *      mode="hidden",
 *      routeName="marello_tax_taxjurisdiction_index",
 *      routeView="marello_tax_taxjurisdiction_view",
 *      routeUpdate="marello_tax_taxjurisdiction_update",
 *      defaultValues={
 *          "dataaudit"={
 *              "auditable"=true
 *          },
 *          "security"={
 *             "type"="ACL",
 *             "group_name"=""
 *         }
 *      }
 * )
 */
#[ORM\Table('marello_tax_tax_jurisdiction')]
#[ORM\UniqueConstraint(name: 'marello_tax_jurisdiction_codeidx', columns: ['code'])]
#[ORM\Entity(repositoryClass: \Marello\Bundle\TaxBundle\Entity\Repository\TaxJurisdictionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TaxJurisdiction implements DatesAwareInterface
{
    use DatesAwareTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=10,
     *              "identity"=true
     *          },
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'code', type: 'string', length: 255, unique: true)]
    protected $code;

    /**
     * @var string
     *
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    protected $description;

    /**
     * @var Country
     *
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'country_code', referencedColumnName: 'iso2_code')]
    #[ORM\ManyToOne(targetEntity: \Oro\Bundle\AddressBundle\Entity\Country::class)]
    protected $country;

    /**
     * @var Region
     *
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'region_code', referencedColumnName: 'combined_code')]
    #[ORM\ManyToOne(targetEntity: \Oro\Bundle\AddressBundle\Entity\Region::class)]
    protected $region;

    /**
     * @var string
     *
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'region_text', type: 'string', length: 255, nullable: true)]
    protected $regionText;

    /**
     * @var Collection|ZipCode[]
     *
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\OneToMany(targetEntity: \Marello\Bundle\TaxBundle\Entity\ZipCode::class, mappedBy: 'taxJurisdiction', cascade: ['all'], orphanRemoval: true)]
    protected $zipCodes;

    /**
     * @var array $data
     *
     * @ConfigField(
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
     * Constructor
     */
    public function __construct()
    {
        $this->zipCodes = new ArrayCollection();
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
     * Set code
     *
     * @param string $code
     *
     * @return TaxJurisdiction
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return TaxJurisdiction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set country
     *
     * @param Country $country
     *
     * @return TaxJurisdiction
     */
    public function setCountry(Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set region
     *
     * @param Region $region
     *
     * @return TaxJurisdiction
     */
    public function setRegion(Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set regionText
     *
     * @param string $regionText
     *
     * @return TaxJurisdiction
     */
    public function setRegionText($regionText)
    {
        $this->regionText = $regionText;

        return $this;
    }

    /**
     * Get regionText
     *
     * @return string
     */
    public function getRegionText()
    {
        return $this->regionText;
    }

    /**
     * Get name of region
     *
     * @return string
     */
    public function getRegionName()
    {
        return $this->getRegion() ? $this->getRegion()->getName() : $this->getRegionText();
    }

    /**
     * Add zipCode
     *
     * @param ZipCode $zipCode
     *
     * @return TaxJurisdiction
     */
    public function addZipCode(ZipCode $zipCode)
    {
        if (!$this->zipCodes->contains($zipCode)) {
            $zipCode->setTaxJurisdiction($this);
            $this->zipCodes[] = $zipCode;
        }

        return $this;
    }

    /**
     * Remove zipCode
     *
     * @param ZipCode $zipCode
     * @return $this
     */
    public function removeZipCode(ZipCode $zipCode)
    {
        if ($this->zipCodes->contains($zipCode)) {
            $this->zipCodes->removeElement($zipCode);
        }

        return $this;
    }

    /**
     * Get zipCodes
     *
     * @return Collection
     */
    public function getZipCodes()
    {
        return $this->zipCodes;
    }

    /**
     * @param array $data
     *
     * @return TaxJurisdiction
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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->code;
    }

    #[ORM\PreUpdate]
    public function preUpdateTimestamp()
    {
        $this->updated = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    #[ORM\PrePersist]
    public function prePersistTimestamp()
    {
        $this->created = $this->updated = new \DateTime('now', new \DateTimeZone('UTC'));
    }
}
