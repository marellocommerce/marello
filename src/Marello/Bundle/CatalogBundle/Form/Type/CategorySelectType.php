<?php

namespace Marello\Bundle\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;

class CategorySelectType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_catalog_category_select';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'categories',
                'create_enabled'  => false,
                'grid_name' => 'marello-categories-grid',
                'configs'            => [
                    'placeholder' => 'marello.catalog.category.form.choose_category',
                    'result_template_twig' => '@MarelloCatalog/Category/Autocomplete/result.html.twig',
                    'selection_template_twig' => '@MarelloCatalog/Category/Autocomplete/selection.html.twig',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return OroEntitySelectOrCreateInlineType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
