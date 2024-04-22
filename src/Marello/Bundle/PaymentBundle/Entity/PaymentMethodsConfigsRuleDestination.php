<?php

namespace Marello\Bundle\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\AddressBundle\Entity\Country;
use Oro\Bundle\AddressBundle\Entity\Region;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

/**
 * @Config(
 *     mode="hidden",
 * )
 */
#[ORM\Table('marello_payment_mtds_cfgs_rl_d')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class PaymentMethodsConfigsRuleDestination implements ExtendEntityInterface
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
     * @var PaymentMethodsConfigsRule
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'configs_rule_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRule::class, inversedBy: 'destinations')]
    protected $methodsConfigsRule;

    /**
     * @var Collection|PaymentMethodsConfigsRuleDestinationPostalCode[]
     */
    #[ORM\OneToMany(targetEntity: \Marello\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRuleDestinationPostalCode::class, mappedBy: 'destination', cascade: ['ALL'], fetch: 'EAGER', orphanRemoval: true)]
    protected $postalCodes;

    /**
     * @var Region
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=20,
     *              "short"=true,
     *              "identity"=true
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
     *          "importexport"={
     *              "order"=30
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'region_text', type: 'string', length: 255, nullable: true)]
    protected $regionText;

    /**
     * @var Country
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=40,
     *              "short"=true,
     *              "identity"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'country_code', referencedColumnName: 'iso2_code', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Oro\Bundle\AddressBundle\Entity\Country::class)]
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
