<?php

namespace Marello\Bundle\UPSBundle\Tests\Functional\EventListener;

use Marello\Bundle\ShippingBundle\Entity\ShippingMethodConfig;
use Marello\Bundle\ShippingBundle\Entity\ShippingMethodTypeConfig;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use Marello\Bundle\UPSBundle\Entity\UPSSettings;
use Marello\Bundle\UPSBundle\Entity\ShippingService;
use Marello\Bundle\UPSBundle\Method\UPSShippingMethod;
use Marello\Bundle\UPSBundle\Tests\Functional\DataFixtures\LoadShippingMethodsConfigsRules;

class UPSTransportEntityListenerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->initClient([], static::generateBasicAuthHeader());
        $this->loadFixtures(
            [
                LoadShippingMethodsConfigsRules::class
            ]
        );
    }

    public function testPostUpdate()
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        /** @var Channel $upsChannel */
        $upsChannel = $this->getReference('ups:channel_1');
        /** @var UPSSettings $upsTransport */
        $upsTransport = $upsChannel->getTransport();
        $applShipServices = $upsTransport->getApplicableShippingServices();
        /** @var ShippingService $toBeDeletedService */
        $toBeDeletedService = $applShipServices->first();

        $configuredMethods = $em
            ->getRepository(ShippingMethodConfig::class)
            ->findBy([
                'method' => UPSShippingMethod::IDENTIFIER . '_' . $upsChannel->getId()]);

        $typesBefore = $em
            ->getRepository(ShippingMethodTypeConfig::class)
            ->findBy(['methodConfig' => $configuredMethods, 'type' => $toBeDeletedService->getCode()]);

        static::assertNotEmpty($typesBefore);

        $upsTransport->removeApplicableShippingService($toBeDeletedService);
        $em->persist($upsTransport);
        $em->flush();

        $typesAfter = $em
            ->getRepository(ShippingMethodTypeConfig::class)
            ->findBy(['methodConfig' => $configuredMethods, 'type' => $toBeDeletedService->getCode()]);

        static::assertEmpty($typesAfter);
    }
}
