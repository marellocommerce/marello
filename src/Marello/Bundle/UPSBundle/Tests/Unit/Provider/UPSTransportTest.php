<?php

namespace Marello\Bundle\UPSBundle\Tests\Unit\Provider;

use Marello\Bundle\UPSBundle\Client\Url\Provider\UpsClientUrlProviderInterface;
use Marello\Bundle\UPSBundle\Entity\UPSSettings;
use Marello\Bundle\UPSBundle\Form\Type\UPSTransportSettingsType;
use Marello\Bundle\UPSBundle\Model\Request\PriceRequest;
use Marello\Bundle\UPSBundle\Provider\UPSTransport;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestClientFactoryInterface;
use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface;
use Oro\Component\Testing\Unit\EntityTrait;
use Psr\Log\LoggerInterface;

class UPSTransportTest extends \PHPUnit_Framework_TestCase
{
    use EntityTrait;

    const PRODUCTION_URL = 'prod.example.org';
    const TEST_URL = 'test.example.org';

    /**
     * @var RestClientFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $clientFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $client;

    /**
     * @var UPSTransport
     */
    protected $transport;

    /**
     * @var UpsClientUrlProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $upsClientUrlProviderMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    protected function setUp()
    {
        $this->client = $this->createMock('Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestClientInterface');

        $this->clientFactory = $this->createMock(
            'Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestClientFactoryInterface'
        );

        $this->logger = $this->createMock(LoggerInterface::class);

        $this->upsClientUrlProviderMock = $this->createMock(UpsClientUrlProviderInterface::class);

        $this->transport = new UPSTransport($this->upsClientUrlProviderMock, $this->logger);
        $this->transport->setRestClientFactory($this->clientFactory);
    }

    public function testGetLabel()
    {
        static::assertEquals('marello.ups.transport.label', $this->transport->getLabel());
    }

    public function testGetSettingsFormType()
    {
        static::assertEquals(UPSTransportSettingsType::class, $this->transport->getSettingsFormType());
    }

    public function testGetSettingsEntityFQCN()
    {
        static::assertEquals('Marello\Bundle\UPSBundle\Entity\UPSSettings', $this->transport->getSettingsEntityFQCN());
    }

    public function testGetPrices()
    {
        /** @var PriceRequest|\PHPUnit_Framework_MockObject_MockObject $rateRequest * */
        $rateRequest = $this->createMock(PriceRequest::class);

        $integration = new Channel();
        $transportEntity = new UPSSettings();
        $integration->setTransport($transportEntity);

        $this->clientFactory->expects(static::once())
            ->method('createRestClient')
            ->willReturn($this->client);

        $restResponse = $this->createMock('Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface');

        $json = '{
                   "RateResponse":{
                      "RatedShipment":{
                         "Service": {
                            "Code":"02"
                         },
                         "TotalCharges":{
                            "CurrencyCode":"USD",
                            "MonetaryValue":"8.60"
                         }
                      }
                   }
                }';

        $restResponse->expects(static::any())
            ->method('json')
            ->willReturn($json);

        $this->client->expects(static::once())
            ->method('post')
            ->willReturn($restResponse);

        $this->transport->getPriceResponse($rateRequest, $transportEntity);
    }

    public function testGetPricesException()
    {
        /** @var PriceRequest|\PHPUnit_Framework_MockObject_MockObject $rateRequest * */
        $rateRequest = $this->createMock(PriceRequest::class);

        $integration = new Channel();
        /** @var UPSSettings $transportEntity */
        $transportEntity = $this->getEntity(UPSSettings::class, ['id' => '123']);
        $integration->setTransport($transportEntity);

        $this->clientFactory->expects(static::once())
            ->method('createRestClient')
            ->willReturn($this->client);

        $restResponse = $this->createMock('Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestResponseInterface');

        $json = '{
            "Fault":{
                "faultcode":"Client", 
                "faultstring":"An exception has been raised as a result of client data.", 
                "detail":{
                    "Errors":{
                        "ErrorDetail":{
                            "Severity":"Hard", 
                            "PrimaryErrorCode":{
                                "Code":"111100", 
                                "Description":"The requested service is invalid from the selected origin."
                            }
                        }
                    }
                }
            }
        }';

        $jsonArr = json_decode($json, true);

        $restResponse->expects(static::any())
            ->method('json')
            ->willReturn($jsonArr)
        ;

        $this->client->expects(static::once())
            ->method('post')
            ->willReturn($restResponse)
        ;

        $this->logger->expects(static::once())
            ->method('error')
            ->with(
                sprintf(
                    'Price request failed for transport #%s. %s',
                    $transportEntity->getId(),
                    json_encode($jsonArr['Fault'])
                )
            )
        ;

        $this->transport->getPriceResponse($rateRequest, $transportEntity);
    }

    /**
     * @dataProvider clientBaseOptionDataProvider
     *
     * @param bool   $testMode
     * @param string $expectedUrl
     */
    public function testClientBaseUrl($testMode, $expectedUrl)
    {
        /** @var UPSSettings $transportEntity */
        $transportEntity = $this->getEntity(
            UPSSettings::class,
            [
                'id' => '123',
                'upsTestMode' => $testMode,
            ]
        );

        $integration = new Channel();
        $integration->setTransport($transportEntity);

        $this->client->expects(static::once())
            ->method('post')
            ->willReturn(
                $this->createMock(RestResponseInterface::class)
            );

        $this->upsClientUrlProviderMock
            ->expects($this->once())
            ->method('getUpsUrl')
            ->with($testMode)
            ->willReturn($expectedUrl);

        $this->clientFactory->expects(static::once())
            ->method('createRestClient')
            ->with($expectedUrl)
            ->willReturn($this->client);

        $this->transport->getPriceResponse($this->createRateRequestMock(), $transportEntity);
    }

    /**
     * @return array
     */
    public function clientBaseOptionDataProvider()
    {
        return [
            ['testMode' => false, 'expectedUrl' => self::PRODUCTION_URL],
            ['testMode' => true, 'expectedUrl' => self::TEST_URL],
        ];
    }

    /**
     * @return PriceRequest|\PHPUnit_Framework_MockObject_MockObject $rateRequest
     */
    private function createRateRequestMock()
    {
        return $this->createMock(PriceRequest::class);
    }
}
