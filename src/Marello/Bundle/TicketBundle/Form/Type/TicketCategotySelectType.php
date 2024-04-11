<?php

namespace Marello\Bundle\TicketBundle\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketCategotySelectType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_ticket_category_select';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'marello_ticket_category',
                'create_form_route'  => 'marello_ticket_category_create',
                'grid_name'          => 'marello-ticket_category-select-grid',
                'create_enabled'     => true,
                'configs'            => [
                    'placeholder' => 'marello.ticket.category.form.choose',
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