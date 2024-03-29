<?php

namespace Marello\Bundle\PaymentTermBundle\Method\Factory;

use Marello\Bundle\PaymentBundle\Method\Factory\IntegrationPaymentMethodFactoryInterface;
use Marello\Bundle\PaymentTermBundle\Method\PaymentTermMethod;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\IntegrationBundle\Provider\IntegrationIconProviderInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;

class PaymentTermMethodFromChannelFactory implements IntegrationPaymentMethodFactoryInterface
{
    /**
     * @var IntegrationIdentifierGeneratorInterface
     */
    private $identifierGenerator;

    /**
     * @var LocalizationHelper
     */
    private $localizationHelper;

    /**
     * @var IntegrationIconProviderInterface
     */
    private $integrationIconProvider;

    /**
     * @param IntegrationIdentifierGeneratorInterface $identifierGenerator
     * @param LocalizationHelper                      $localizationHelper
     * @param IntegrationIconProviderInterface        $integrationIconProvider
     */
    public function __construct(
        IntegrationIdentifierGeneratorInterface $identifierGenerator,
        LocalizationHelper $localizationHelper,
        IntegrationIconProviderInterface $integrationIconProvider
    ) {
        $this->identifierGenerator = $identifierGenerator;
        $this->localizationHelper = $localizationHelper;
        $this->integrationIconProvider = $integrationIconProvider;
    }

    /**
     * @param Channel $channel
     *
     * @return PaymentTermMethod
     */
    public function create(Channel $channel)
    {
        $id = $this->identifierGenerator->generateIdentifier($channel);
        $label = $this->getChannelLabel($channel);
        $icon = $this->getIcon($channel);

        return new PaymentTermMethod($id, $label, $icon, $channel->isEnabled());
    }

    /**
     * @param Channel $channel
     *
     * @return string
     */
    private function getChannelLabel(Channel $channel)
    {
        $transport = $channel->getTransport();

        return $transport->getLabels() ?
            (string) $this->localizationHelper->getLocalizedValue($transport->getLabels()) : null;
    }

    /**
     * @param Channel $channel
     *
     * @return string|null
     */
    private function getIcon(Channel $channel)
    {
        return $this->integrationIconProvider->getIcon($channel);
    }
}
