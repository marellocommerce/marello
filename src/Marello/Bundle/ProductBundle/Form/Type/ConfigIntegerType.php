<?php

namespace Marello\Bundle\ProductBundle\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ConfigIntegerType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_config_integer';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): ?string
    {
        return IntegerType::class;
    }
}
