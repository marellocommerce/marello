<?php

namespace Marello\Bundle\UPSBundle\Connection\Validator;

use Oro\Bundle\IntegrationBundle\Provider\Rest\Exception\RestException;
use Marello\Bundle\UPSBundle\Client\Factory\UpsClientFactoryInterface;
use Marello\Bundle\UPSBundle\Connection\Validator\Request\Factory\UpsConnectionValidatorRequestFactoryInterface;
use Marello\Bundle\UPSBundle\Connection\Validator\Result\Factory\UpsConnectionValidatorResultFactoryInterface;
use Marello\Bundle\UPSBundle\Entity\UPSSettings;
use Psr\Log\LoggerInterface;

class UpsConnectionValidator implements UpsConnectionValidatorInterface
{
    /**
     * @var UpsConnectionValidatorRequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var UpsClientFactoryInterface
     */
    private $clientFactory;

    /**
     * @var UpsConnectionValidatorResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param UpsConnectionValidatorRequestFactoryInterface $requestFactory
     * @param UpsClientFactoryInterface                     $clientFactory
     * @param UpsConnectionValidatorResultFactoryInterface  $resultFactory
     * @param LoggerInterface                               $logger
     */
    public function __construct(
        UpsConnectionValidatorRequestFactoryInterface $requestFactory,
        UpsClientFactoryInterface $clientFactory,
        UpsConnectionValidatorResultFactoryInterface $resultFactory,
        LoggerInterface $logger
    ) {
        $this->requestFactory = $requestFactory;
        $this->clientFactory = $clientFactory;
        $this->resultFactory = $resultFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function validateConnectionByUpsSettings(UPSSettings $transport)
    {
        $request = $this->requestFactory->createByTransport($transport);
        $client = $this->clientFactory->createUpsClient($transport->isUpsTestMode());

        try {
            $response = $client->post($request->getUrl(), $request->getRequestData());
        } catch (RestException $e) {
            $this->logger->error($e->getMessage());

            return $this->resultFactory->createExceptionResult($e);
        }

        return $this->resultFactory->createResultByUpsClientResponse($response);
    }
}
