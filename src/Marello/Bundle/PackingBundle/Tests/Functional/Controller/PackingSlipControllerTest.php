<?php

namespace Marello\Bundle\PackingBundle\Tests\Functional\Controller;

use Marello\Bundle\PackingBundle\Tests\Functional\DataFixtures\LoadPackingSlipData;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PackingSlipControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());
        $this->loadFixtures([
            LoadPackingSlipData::class
        ]);
    }

    public function testIndexAction()
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('marello_packing_packingslip_index')
        );

        $this->assertHtmlResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
        $this->assertStringContainsString('marello-packingslips', $crawler->html());
    }

    public function testViewAction()
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('marello_packing_packingslip_view', ['id' => $this->getReference('packing_slip.0')->getId()])
        );

        $this->assertHtmlResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
        $this->assertStringContainsString('marello-packingslip-items', $crawler->html());
    }
}
