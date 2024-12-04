<?php

namespace Marello\Bundle\InventoryBundle\Tests\Functional\Api;

use Symfony\Component\HttpFoundation\Response;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\CoreBundle\Tests\Functional\RestJsonApiTestCase;
use Marello\Bundle\SalesBundle\Tests\Functional\DataFixtures\LoadSalesData;
use Marello\Bundle\ProductBundle\Tests\Functional\DataFixtures\LoadProductData;
use Marello\Bundle\InventoryBundle\Tests\Functional\DataFixtures\LoadInventoryData;

class InventoryItemJsonApiTest extends RestJsonApiTestCase
{
    const TESTING_ENTITY = 'marelloinventoryitems';

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            LoadInventoryData::class
        ]);
    }

    /**
     * Test cget; Get a list of  virtual(balanced) inventory items
     */
    public function testGetListOfInventoryItems()
    {
        $response = $this->cget(['entity' => self::TESTING_ENTITY], []);

        $this->assertJsonResponse($response);
        $this->assertResponseStatusCodeEquals($response, Response::HTTP_OK);
        $this->assertResponseCount(6, $response);
        $this->assertResponseContains('cget_inventoryitem_list.yml', $response);
    }

    /**
     * Test filtering of inventory item by Product Sku
     */
    public function testFilterInventoryItemByProductSku()
    {
        /** @var Product $product */
        $product = $this->getReference(LoadProductData::PRODUCT_1_REF);
        $response = $this->cget(
            ['entity' => self::TESTING_ENTITY],
            [
                'filter' => ['product' =>  $product->getSku()]
            ]
        );
        $this->assertJsonResponse($response);
        $this->assertResponseContains('get_inventoryitem_by_product_sku.yml', $response);
    }

    /**
     * Test if we're not allowed to create a virtual inventory level via the API
     */
    public function testFailToCreateInventoryItem()
    {
        $response = $this->post(
            ['entity' => self::TESTING_ENTITY],
            'inventoryitem_create.yml',
            [],
            false
        );

        $this->assertJsonResponse($response);
        $this->assertResponseStatusCodeEquals($response, Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
