<?php

namespace Marello\Bundle\ProductBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Tests\Functional\DataFixtures\LoadProductData;

class VariantControllerTest extends WebTestCase
{
    public function setUp(): void
    {
        $this->initClient(
            [],
            $this->generateBasicAuthHeader()
        );

        $this->loadFixtures([
            LoadProductData::class,
        ]);
    }

    public function testCreateVariantAvailable()
    {
        /** @var Product $product */
        $product = $this->getReference(LoadProductData::PRODUCT_1_REF);

        $this->client->request(
            'GET',
            $this->getUrl('marello_product_create_variant', ['id' => $product->getId()])
        );

        $response = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($response, Response::HTTP_OK);
    }

    public function testCreateVariant()
    {
        /** @var Product $product */
        $product = $this->getContainer()
            ->get('doctrine')
            ->getRepository(Product::class)
            ->findOneBy(['variant' => null]);

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('marello_product_create_variant', ['id' => $product->getId()])
        );

        $form = $crawler
            ->selectButton('Save and Close')
            ->form();

        $this->client->submit($form);

        $response = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($response, Response::HTTP_FOUND);

        $product = $this->getContainer()
            ->get('doctrine')
            ->getRepository(Product::class)
            ->find($product->getId());

        $this->assertNotNull($product->getVariant());
    }
}
