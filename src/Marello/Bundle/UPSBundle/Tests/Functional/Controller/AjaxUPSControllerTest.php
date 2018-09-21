<?php

namespace Marello\Bundle\UPSBundle\Tests\Functional\Controller;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Marello\Bundle\UPSBundle\Entity\ShippingService;
use Marello\Bundle\UPSBundle\Tests\Functional\DataFixtures\LoadShippingServices;

class AjaxUPSControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient([], static::generateBasicAuthHeader());
        $this->loadFixtures([LoadShippingServices::class]);
    }

    /**
     * @dataProvider getShippingServicesByCountryDataProvider
     *
     * @param string $countryCode
     * @param array $expectedServices
     */
    public function testGetShippingServicesByCountryAction($countryCode, array $expectedServices)
    {
        $this
            ->client
            ->request('GET', $this->getUrl('marello_ups_country_shipping_services', ['code' => $countryCode]));

        $result = static::getJsonResponseContent($this->client->getResponse(), 200);

        /** @var ShippingService[]|array $expectedShippingServices */
        $expectedShippingServices = $this->getEntitiesByReferences($expectedServices);
        $expected = [];
        foreach ($expectedShippingServices as $service) {
            $expected[] = ['id' => $service->getId(), 'description' => $service->getDescription()];
        }

        static::assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getShippingServicesByCountryDataProvider()
    {
        return [
            [
                'country' => 'ZX',
                'expectedServices' => [
                    'ups.shipping_service.1',
                    'ups.shipping_service.2',
                ]
            ],
            [
                'country' => 'ZY',
                'expectedServices' => [
                    'ups.shipping_service.3',
                    'ups.shipping_service.4',
                ]
            ],
            [
                'country' => 'ZZ',
                'expectedServices' => [
                    'ups.shipping_service.5',
                    'ups.shipping_service.6',
                ]
            ],
        ];
    }

    /**
     * @param array $rules
     * @return array
     */
    protected function getEntitiesByReferences(array $rules)
    {
        return array_map(function ($ruleReference) {
            return $this->getReference($ruleReference);
        }, $rules);
    }
}
