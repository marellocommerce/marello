<?php

namespace Marello\Bundle\UPSBundle\Client\Factory\Basic;

use Oro\Bundle\IntegrationBundle\Provider\Rest\Client\RestClientFactoryInterface;
use Marello\Bundle\UPSBundle\Client\Factory\UpsClientFactoryInterface;
use Marello\Bundle\UPSBundle\Client\Url\Provider\UpsClientUrlProviderInterface;

class BasicUpsClientFactory implements UpsClientFactoryInterface
{
    /**
     * @var RestClientFactoryInterface
     */
    private $restClientFactory;

    /**
     * @var UpsClientUrlProviderInterface
     */
    private $upsClientUrlProvider;

    /**
     * @param RestClientFactoryInterface    $restClientFactory
     * @param UpsClientUrlProviderInterface $upsClientUrlProvider
     */
    public function __construct(
        RestClientFactoryInterface $restClientFactory,
        UpsClientUrlProviderInterface $upsClientUrlProvider
    ) {
        $this->restClientFactory = $restClientFactory;
        $this->upsClientUrlProvider = $upsClientUrlProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function createUpsClient($isTestMode)
    {
        $url = $this->upsClientUrlProvider->getUpsUrl($isTestMode);

        return $this->restClientFactory->createRestClient($url, []);
    }
}
