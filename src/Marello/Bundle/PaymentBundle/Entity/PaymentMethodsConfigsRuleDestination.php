<?php

namespace Marello\Bundle\PaymentBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\AddressBundle\Entity\Region;
use Oro\Bundle\AddressBundle\Entity\Country;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

#[ORM\Table('marello_payment_mtds_cfgs_rl_d')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(mode: 'hidden')]
class PaymentMethodsConfigsRuleDestination implements ExtendEntityInterface
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
     * @var PaymentMethodsConfigsRule
     */
    #[ORM\JoinColumn(name: 'configs_rule_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: PaymentMethodsConfigsRule::class, inversedBy: 'destinations')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected $methodsConfigsRule;

    /**
     * @var Collection|PaymentMethodsConfigsRuleDestinationPostalCode[]
     */
    #[ORM\OneToMany(
        mappedBy: 'destination',
        targetEntity: PaymentMethodsConfigsRuleDestinationPostalCode::class,
        cascade: ['ALL'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    protected $postalCodes;

    /**
     * @var Region
     */
    #[ORM\JoinColumn(name: 'region_code', referencedColumnName: 'combined_code')]
    #[ORM\ManyToOne(targetEntity: Region::class)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 20, 'short' => true, 'identity' => true]])]
    protected $region;

    /**
     * @var string
     */
    #[ORM\Column(name: 'region_text', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 30]])]
    protected $regionText;

    /**
     * @var Country
     */
    #[ORM\JoinColumn(name: 'country_code', referencedColumnName: 'iso2_code', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 40, 'short' => true, 'identity' => true]])]
    protected $country;

    public function __construct()
    {
        $this->postalCodes = new ArrayCollection();
    }

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
     * @return Collection|PaymentMethodsConfigsRuleDestinationPostalCode[]
     */
    public function getPostalCodes()
    {
        return $this->postalCodes;
    }

    /**
     * @param PaymentMethodsConfigsRuleDestinationPostalCode $postalCode
     * @return $this
     */
    public function addPostalCode(PaymentMethodsConfigsRuleDestinationPostalCode $postalCode)
    {
        if (!$this->postalCodes->contains($postalCode)) {
            $postalCode->setDestination($this);
            $this->postalCodes->add($postalCode);
        }

        return $this;
    }

    /**
     * @param PaymentMethodsConfigsRuleDestinationPostalCode $postalCode
     * @return $this
     */
    public function removePostalCode(PaymentMethodsConfigsRuleDestinationPostalCode $postalCode)
    {
        if ($this->postalCodes->contains($postalCode)) {
            $this->postalCodes->removeElement($postalCode);
        }

        return $this;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param Region $region
     * @return $this
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get name of region
     *
     * @return string
     */
    public function getRegionName()
    {
        return $this->getRegion() ? $this->getRegion()->getName() : '';
    }

    /**
     * Get code of region
     *
     * @return string
     */
    public function getRegionCode()
    {
        return $this->getRegion() ? $this->getRegion()->getCode() : '';
    }

    /**
     * @return string
     */
    public function getRegionText()
    {
        return $this->regionText;
    }

    /**
     * @param string $regionText
     * @return $this
     */
    public function setRegionText($regionText)
    {
        $this->regionText = $regionText;

        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Get name of country
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->getCountry() ? $this->getCountry()->getName() : '';
    }

    /**
     * @param Country $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country ISO2 code
     *
     * @return string
     */
    public function getCountryIso2()
    {
        return $this->getCountry() ? $this->getCountry()->getIso2Code() : '';
    }

    /**
     * Get country ISO3 code
     *
     * @return string
     */
    public function getCountryIso3()
    {
        return $this->getCountry() ? $this->getCountry()->getIso3Code() : '';
    }

    /**
     * Convert address to string
     *
     * @return string
     */
    public function __toString()
    {
        $countryPostalStr = implode(
            ' ',
            array_filter([
                $this->getCountry(),
                implode(', ', array_map(function (PaymentMethodsConfigsRuleDestinationPostalCode $postalCode) {
                    return (string)$postalCode;
                }, $this->postalCodes->getValues())),
            ])
        );

        return implode(', ', array_filter([$this->getRegionName(), $countryPostalStr]));
    }
}
