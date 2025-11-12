<?php

namespace Marello\Bundle\PricingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\FormBundle\Form\Type\OroChoiceType;

use Marello\Bundle\PricingBundle\Provider\CompanyPriceProviderRegistry;
use Marello\Bundle\PricingBundle\Provider\CompanyPriceProviderInterface;

class CompanyPriceProviderChoiceType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_pricing_price_provider_choice';

    /**
     * @param CompanyPriceProviderRegistry $registry
     */
    public function __construct(
        protected CompanyPriceProviderRegistry $registry,
        protected TranslatorInterface $translator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'choices' => array_flip($this->getOptions())
            ]
        );
    }

    protected function getOptions()
    {
        return array_reduce(
            $this->registry->getPriceProviders(),
            function (array $result, CompanyPriceProviderInterface $provider) {
                if ($provider->isEnabled()) {
                    $result[$provider->getIdentifier()] = $this->translator->trans($provider->getLabel());
                }
                return $result;
            },
            []
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return OroChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
