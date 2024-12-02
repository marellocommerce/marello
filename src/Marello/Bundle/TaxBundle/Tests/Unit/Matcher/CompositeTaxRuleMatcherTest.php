<?php

namespace Marello\Bundle\TaxBundle\Tests\Unit\Matcher;

use PHPUnit\Framework\TestCase;

use Oro\Bundle\AddressBundle\Entity\Region;
use Oro\Component\Testing\Unit\EntityTrait;
use Oro\Bundle\AddressBundle\Entity\Address;
use Oro\Bundle\AddressBundle\Entity\Country;

use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Marello\Bundle\TaxBundle\Entity\TaxRule;
use Marello\Bundle\TaxBundle\Matcher\CompositeTaxRuleMatcher;
use Marello\Bundle\TaxBundle\Matcher\TaxRuleMatcherInterface;
use Marello\Bundle\TaxBundle\Provider\CompanyReverseTaxProvider;

class CompositeTaxRuleMatcherTest extends TestCase
{
    use EntityTrait;

    /**
     * @var CompositeTaxRuleMatcher
     */
    protected $compositeTaxRuleMatcher;

    protected $companyReverseTaxProvider;

    protected function setUp(): void
    {
        $this->companyReverseTaxProvider = $this->createMock(CompanyReverseTaxProvider::class);
        $this->compositeTaxRuleMatcher = new CompositeTaxRuleMatcher($this->companyReverseTaxProvider);
    }

    /**
     * @dataProvider matchProvider
     * @param string $taxCode
     * @param Country $country
     * @param Region $region
     * @param string $regionText
     * @param TaxRule[] $countryMatcherTaxRules
     * @param TaxRule[] $regionMatcherTaxRules
     * @param TaxRule[] $zipCodeMatcherTaxRules
     * @param TaxRule[] $expected
     */
    public function testMatch(
        $taxCode,
        $country,
        $region,
        $regionText,
        $countryMatcherTaxRules,
        $regionMatcherTaxRules,
        $zipCodeMatcherTaxRules,
        $expected
    ) {
        $address = (new Address())
            ->setPostalCode(ZipCodeTaxRuleMatcherTest::POSTAL_CODE)
            ->setCountry($country)
            ->setRegion($region)
            ->setRegionText($regionText);

        $taxCodes = [];
        if ($taxCode) {
            $taxCodes[] = $this->getEntity(TaxCode::class, ['code' => $taxCode]);
        }

        $this->compositeTaxRuleMatcher->addMatcher($this->createMatcherMock($zipCodeMatcherTaxRules));
        $this->compositeTaxRuleMatcher->addMatcher($this->createMatcherMock($regionMatcherTaxRules));
        $this->compositeTaxRuleMatcher->addMatcher($this->createMatcherMock($countryMatcherTaxRules));
        $this->companyReverseTaxProvider
            ->expects($this->any())
            ->method('orderIsTaxable')
            ->willReturn(true);
        $this->assertEquals($expected, $this->compositeTaxRuleMatcher->match($taxCodes, null, $address));

        //cache
        $this->assertEquals($expected, $this->compositeTaxRuleMatcher->match($taxCodes, null, $address));
    }

    /**
     * @param $data
     * @return TaxRuleMatcherInterface|\PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    protected function createMatcherMock($data)
    {
        $matcher = $this->createMock(TaxRuleMatcherInterface::class);
        $matcher
            ->expects($this->any())
            ->method('match')
            ->willReturn($data);

        return $matcher;
    }

    /**
     * @return array
     */
    public function matchProvider()
    {
        $country = new Country('US');
        $region = new Region('US-NY');
        $regionText = 'Alaska';

        $countryMatcherTaxRules = $this->getTaxRule(1);
        $regionMatcherTaxRules = $this->getTaxRule(2);
        $zipCodeMatcherTaxRules = $this->getTaxRule(3);

        return [
            'with region' => [
                'taxCode' => 'TAX_CODE',
                'country' => $country,
                'region' => $region,
                'regionText' => '',
                'countryMatcherRules' => $countryMatcherTaxRules,
                'regionMatcherRules' => $regionMatcherTaxRules,
                'zipCodeMatcherRules' => $zipCodeMatcherTaxRules,
                'expected' => $zipCodeMatcherTaxRules
            ],
            'with regionText' => [
                'taxCode' => 'TAX_CODE',
                'country' => $country,
                'region' => null,
                'regionText' => $regionText,
                'countryMatcherRules' => $countryMatcherTaxRules,
                'regionMatcherRules' => $regionMatcherTaxRules,
                'zipCodeMatcherRules' => $zipCodeMatcherTaxRules,
                'expected' => $zipCodeMatcherTaxRules
            ],
            'no zipcode rules' => [
                'taxCode' => 'TAX_CODE',
                'country' => $country,
                'region' => null,
                'regionText' => $regionText,
                'countryMatcherRules' => $countryMatcherTaxRules,
                'regionMatcherRules' => $regionMatcherTaxRules,
                'zipCodeMatcherRules' => null,
                'expected' => $regionMatcherTaxRules
            ],
            'no zipcode and region rules' => [
                'taxCode' => 'TAX_CODE',
                'country' => $country,
                'region' => null,
                'regionText' => $regionText,
                'countryMatcherRules' => $countryMatcherTaxRules,
                'regionMatcherRules' => null,
                'zipCodeMatcherRules' => null,
                'expected' => $countryMatcherTaxRules
            ],
        ];
    }

    /**
     * @param int $id
     * @return TaxRule
     */
    protected function getTaxRule($id)
    {
        return $this->getEntity(TaxRule::class, ['id' => $id]);
    }
}
