<?php

namespace Marello\Bundle\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Marello\Bundle\ProductBundle\Provider\ProductTypesProvider;

class ProductTypeSelectType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_product_type_select_form';

    /**
     * @var ProductTypesProvider
     */
    private $provider;

    /**
     * @param ProductTypesProvider $provider
     */
    public function __construct(ProductTypesProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices()
        ]);
    }

    /**
     * @return array
     */
    private function getChoices()
    {
        $choices = [];
        foreach ($this->provider->getProductTypes() as $productType) {
            $choices[$productType->getLabel()] = $productType->getName();
        }

        return $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
