<?php

namespace Marello\Bundle\CustomerBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use Marello\Bundle\CustomerBundle\Tests\Functional\DataFixtures\LoadCustomerData;

class CustomerControllerTest extends WebTestCase
{
    const GRID_NAME = 'marello-customer';

    public function setUp(): void
    {
        $this->initClient(
            [],
            $this->generateBasicAuthHeader()
        );

        $this->loadFixtures([
            LoadCustomerData::class
        ]);
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('marello_customer_index'));
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, Response::HTTP_OK);

        $response = $this->client->requestGrid(self::GRID_NAME);
        $result = $this->getJsonResponseContent($response, Response::HTTP_OK);
        $this->assertCount(10, $result['data']);
    }
}
