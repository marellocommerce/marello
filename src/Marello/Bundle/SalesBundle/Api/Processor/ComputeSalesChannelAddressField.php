<?php

namespace Marello\Bundle\SalesBundle\Api\Processor;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Oro\Bundle\ApiBundle\Processor\CustomizeLoadedData\CustomizeLoadedDataContext;

use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\PdfBundle\Provider\LogoPathProvider;

class ComputeSalesChannelAddressField implements ProcessorInterface
{
    const ADDRESS_IDENTIFIER_KEY = 'marello_pdf.company_address';
    const EMAIL_IDENTIFIER_KEY = 'marello_pdf.company_email';
    const PHONE_IDENTIFIER_KEY = 'marello_pdf.company_phone';

    public function __construct(
        protected ConfigManager $config,
        protected DoctrineHelper $doctrineHelper,
        protected LogoPathProvider $provider
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContextInterface $context): void
    {
        /** @var CustomizeLoadedDataContext $context */
        $data = $context->getData();

        $addressFieldName = $context->getResultFieldName('address');
        $emailFieldName = $context->getResultFieldName('email');
        $phoneFieldName = $context->getResultFieldName('phone');
        $logoFieldName = $context->getResultFieldName('logo');

        $salesChannelIdFieldName = $context->getResultFieldName('id');
        if (!$salesChannelIdFieldName || empty($data[$salesChannelIdFieldName])) {
            return;
        }

        $address = $this->loadSalesChannelAddress((int)$data[$salesChannelIdFieldName]);
        $phone = $this->loadSalesChannelPhoneNumber((int)$data[$salesChannelIdFieldName]);
        $email = $this->loadSalesChannelEmail((int)$data[$salesChannelIdFieldName]);
        $logo = $this->loadSalesChannelLogo((int)$data[$salesChannelIdFieldName]);

        $data[$addressFieldName] = $address;
        $data[$emailFieldName] = $email;
        $data[$phoneFieldName] = $phone;
        $data[$logoFieldName] = $logo;

        $context->setData($data);
    }

    /**
     * @param int $salesChannelId
     * @return null|string
     */
    protected function loadSalesChannelAddress(int $salesChannelId): ?string
    {
        $address = $this->getConfigValue(self::ADDRESS_IDENTIFIER_KEY, $salesChannelId);
        if (!$address) {
            return $this->getConfigValue(self::ADDRESS_IDENTIFIER_KEY);
        }
        return $address;
    }

    /**
     * @param int $salesChannelId
     * @return null|string
     */
    protected function loadSalesChannelPhoneNumber(int $salesChannelId): ?string
    {
        $phone = $this->getConfigValue(self::PHONE_IDENTIFIER_KEY, $salesChannelId);
        if (!$phone) {
            return $this->getConfigValue(self::PHONE_IDENTIFIER_KEY);
        }
        return $phone;
    }

    /**
     * @param int $salesChannelId
     * @return null|string
     */
    protected function loadSalesChannelEmail(int $salesChannelId): ?string
    {
        $email = $this->getConfigValue(self::EMAIL_IDENTIFIER_KEY, $salesChannelId);
        if (!$email) {
            return $this->getConfigValue(self::EMAIL_IDENTIFIER_KEY);
        }

        return $email;
    }

    /**
     * @param int $salesChannelId
     * @return null|string
     */
    protected function loadSalesChannelLogo(int $salesChannelId): ?string
    {
        $salesChannel = $this->doctrineHelper->getEntity(SalesChannel::class, $salesChannelId);
        if (!$salesChannel) {
            return null;
        }

        return $this->provider->getLogo($salesChannel);
    }

    /**
     * @param $identifierKey
     * @param $scopeIdentifier
     * @return mixed|null
     */
    private function getConfigValue($identifierKey, $scopeIdentifier = null): ?string
    {
        return $this->config->get($identifierKey, false, false, $scopeIdentifier);
    }
}
