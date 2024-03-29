<?php

namespace Marello\Bundle\TaxBundle\Tests\Functional\Operation;

use Oro\Bundle\ActionBundle\Tests\Functional\ActionTestCase;

use Marello\Bundle\TaxBundle\Entity\TaxRate;
use Marello\Bundle\TaxBundle\Tests\Functional\DataFixtures\LoadTaxRateData;

class TaxRateDeleteOperationTest extends ActionTestCase
{
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());
        $this->client->useHashNavigation(true);

        $this->loadFixtures(
            [
                'Marello\Bundle\TaxBundle\Tests\Functional\DataFixtures\LoadTaxRateData'
            ]
        );
    }

    public function testDelete()
    {
        $taxRate = $this->getReference(LoadTaxRateData::REFERENCE_PREFIX . '.' . LoadTaxRateData::CODE_1);

        $this->assertDeleteOperation(
            $taxRate->getId(),
            TaxRate::class,
            'marello_tax_taxrate_index'
        );
    }
}
