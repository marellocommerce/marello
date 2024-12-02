<?php

namespace Marello\Bundle\SalesBundle\Tests\Functional\Controller;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\ActionBundle\Tests\Functional\OperationAwareTestTrait;

use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\SalesBundle\Form\Type\SalesChannelType;
use Marello\Bundle\SalesBundle\Tests\Functional\DataFixtures\LoadSalesData;
class SalesChannelControllerTest extends WebTestCase
{
    use OperationAwareTestTrait;

    const NAME = 'name';
    const CODE = 'code';
    const CHANNEL_TYPE = 'magento';
    const CURRENCY = 'USD';
    const DEFAULT = true;
    const ACTIVE = true;
    const LOCALIZATION = 1;

    const UPDATED_NAME = 'updatedName';
    const UPDATED_CODE = 'updatedCode';
    const UPDATED_CHANNEL_TYPE = 'marello';
    const UPDATED_CURRENCY = 'USD';
    const UPDATED_DEFAULT = false;
    const UPDATED_ACTIVE = false;
    const UPDATED_LOCALIZATION = 1;

    const SAVE_MESSAGE = 'Sales Channel has been saved successfully';

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->initClient(
            [],
            $this->generateBasicAuthHeader()
        );

        $this->loadFixtures([
            LoadSalesData::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function testIndex()
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('marello_sales_saleschannel_index')
        );

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
        $this->assertStringContainsString('marello-sales-channel', $crawler->html());
    }

    /**
     * {@inheritdoc}
     * @return int
     */
    public function testCreate()
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('marello_sales_saleschannel_create')
        );

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);

        $this->assertSalesChannelSave(
            $crawler,
            self::NAME,
            self::CHANNEL_TYPE,
            self::CURRENCY,
            self::DEFAULT,
            self::ACTIVE,
            self::LOCALIZATION,
            self::CODE
        );

        /** @var SalesChannel $salesChannel */
        $salesChannel = $this->getContainer()->get('doctrine')
            ->getManagerForClass(SalesChannel::class)
            ->getRepository(SalesChannel::class)
            ->findOneBy(['code' => self::CODE]);
        $this->assertNotEmpty($salesChannel);

        return $salesChannel->getId();
    }

    /**
     * {@inheritdoc}
     * @param int $id
     * @return int
     * @depends testCreate
     */
    public function testUpdate($id)
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('marello_sales_saleschannel_update', ['id' => $id])
        );

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);

        $this->assertSalesChannelSave(
            $crawler,
            self::UPDATED_NAME,
            self::UPDATED_CHANNEL_TYPE,
            self::UPDATED_CURRENCY,
            self::UPDATED_DEFAULT,
            self::UPDATED_ACTIVE,
            self::UPDATED_LOCALIZATION
        );

        return $id;
    }

    /**
     * {@inheritdoc}
     * @depends testUpdate
     * @param int $id
     */
    public function testView($id)
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('marello_sales_saleschannel_view', ['id' => $id])
        );

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);

        $this->assertViewPage(
            $crawler->html(),
            self::UPDATED_NAME,
            self::UPDATED_CHANNEL_TYPE,
            self::UPDATED_CURRENCY,
            self::UPDATED_DEFAULT,
            self::UPDATED_ACTIVE,
            self::UPDATED_LOCALIZATION,
            self::CODE
        );
    }
    
    /**
     * {@inheritdoc}
     * @depends testUpdate
     * @param int $id
     */
    public function testDelete($id)
    {
        $operationName = 'DELETE';
        $this->client->request(
            'POST',
            $this->getUrl(
                'oro_action_operation_execute',
                [
                    'operationName' => $operationName,
                    'entityClass'   => SalesChannel::class,
                    'entityId'      => $id,
                ]
            ),
            $this->getOperationExecuteParams($operationName, $id, SalesChannel::class),
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );
        $this->assertJsonResponseStatusCodeEquals($this->client->getResponse(), 200);
        $this->assertEquals(
            [
                'success'     => true,
                'message'     => '',
                'messages'    => [],
                'redirectUrl' => $this->getUrl('marello_sales_saleschannel_index'),
                'pageReload' => true
            ],
            json_decode($this->client->getResponse()->getContent(), true)
        );

        $this->client->request('GET', $this->getUrl('marello_sales_saleschannel_view', ['id' => $id]));

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 404);
    }

    /**
     * {@inheritdoc}
     * @param Crawler $crawler
     * @param string $name
     * @param string $channelType
     * @param string $currency
     * @param bool $default
     * @param bool $active
     * @param int $localizationId
     * @param string $code
     */
    protected function assertSalesChannelSave(
        Crawler $crawler,
        $name,
        $channelType,
        $currency,
        $default,
        $active,
        $localizationId,
        $code = null
    ) {
        $form = $crawler->selectButton('Save and Close')->form();
        $formData = $form->getPhpValues();
        $formData['input_action'] = '{"route":"marello_sales_saleschannel_view","params":{"id":"$id"}}';
        $formData[SalesChannelType::BLOCK_PREFIX]['name'] = $name;
        if ($code) {
            $formData[SalesChannelType::BLOCK_PREFIX]['code'] = $code;
        }
        $formData[SalesChannelType::BLOCK_PREFIX]['channelType'] = $channelType;
        $formData[SalesChannelType::BLOCK_PREFIX]['currency'] = $currency;
        $formData[SalesChannelType::BLOCK_PREFIX]['default'] = $default;
        $formData[SalesChannelType::BLOCK_PREFIX]['active'] = $active;
        $formData[SalesChannelType::BLOCK_PREFIX]['localization'] = $localizationId;

        $this->client->followRedirects(true);
        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $formData);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $html = $crawler->html();

        $this->assertStringContainsString(self::SAVE_MESSAGE, $html);
        
        $this->assertViewPage(
            $html,
            $name,
            $channelType,
            $currency,
            $default,
            $active,
            $localizationId,
            $code
        );
    }
    
    /**
     * {@inheritdoc}
     * @param string $html
     * @param string $name
     * @param string $code
     * @param string $channelType
     * @param string $currency
     * @param bool $default
     * @param bool $active
     * @param int $localizationId
     */
    protected function assertViewPage(
        $html,
        $name,
        $channelType,
        $currency,
        $default,
        $active,
        $localizationId,
        $code = null
    ) {
        /** @var Localization $localization */
        $localization = $this->getContainer()->get('doctrine')
            ->getManagerForClass(Localization::class)
            ->getRepository(Localization::class)
            ->find($localizationId);
        
        $this->assertStringContainsString($name, $html);
        if ($code) {
            $this->assertStringContainsString($code, $html);
        }
        $this->assertStringContainsString($channelType, $html);
        $this->assertStringContainsString($currency, $html);
        $this->assertStringContainsString($default ? 'Yes' : 'No', $html);
        $this->assertStringContainsString($active ? 'Yes' : 'No', $html);
        $this->assertStringContainsString($localization->getLanguageCode(), $html);
    }
}
