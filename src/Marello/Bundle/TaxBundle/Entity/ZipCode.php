<?php

namespace Marello\Bundle\TaxBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;

#[ORM\Table('marello_tax_zip_code')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(mode: 'hidden')]
class ZipCode implements DatesAwareInterface
{
    use DatesAwareTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'zip_code', type: Types::STRING, length: 255, nullable: true)]
    protected $zipCode;

    /**
     * @var string
     */
    #[ORM\Column(name: 'zip_range_start', type: Types::STRING, length: 255, nullable: true)]
    protected $zipRangeStart;

    /**
     * @var string
     */
    #[ORM\Column(name: 'zip_range_end', type: Types::STRING, length: 255, nullable: true)]
    protected $zipRangeEnd;

    /**
     * @var TaxJurisdiction
     */
    #[ORM\JoinColumn(name: 'tax_jurisdiction_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\TaxBundle\Entity\TaxJurisdiction::class, inversedBy: 'zipCodes', cascade: ['persist'])]
    protected $taxJurisdiction;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->zipCode;
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
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return ZipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set zipRangeStart
     *
     * @param string $zipRangeStart
     *
     * @return ZipCode
     */
    public function setZipRangeStart($zipRangeStart)
    {
        $this->zipRangeStart = $zipRangeStart;

        return $this;
    }

    /**
     * Get zipRangeStart
     *
     * @return string
     */
    public function getZipRangeStart()
    {
        return $this->zipRangeStart;
    }

    /**
     * Set zipRangeEnd
     *
     * @param string $zipRangeEnd
     *
     * @return ZipCode
     */
    public function setZipRangeEnd($zipRangeEnd)
    {
        $this->zipRangeEnd = $zipRangeEnd;

        return $this;
    }

    /**
     * Get zipRangeEnd
     *
     * @return string
     */
    public function getZipRangeEnd()
    {
        return $this->zipRangeEnd;
    }

    /**
     * Is this code single valued
     *
     * @return bool
     */
    public function isSingleZipCode()
    {
        return $this->getZipCode() !== null;
    }

    /**
     * Set taxJurisdiction
     *
     * @param TaxJurisdiction $taxJurisdiction
     *
     * @return ZipCode
     */
    public function setTaxJurisdiction(TaxJurisdiction $taxJurisdiction)
    {
        $this->taxJurisdiction = $taxJurisdiction;

        return $this;
    }

    /**
     * Get taxJurisdiction
     *
     * @return TaxJurisdiction
     */
    public function getTaxJurisdiction()
    {
        return $this->taxJurisdiction;
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
