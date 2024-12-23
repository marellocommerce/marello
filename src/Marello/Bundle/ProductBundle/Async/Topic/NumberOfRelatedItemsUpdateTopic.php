<?php

namespace Marello\Bundle\ProductBundle\Async\Topic;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Component\MessageQueue\Topic\AbstractTopic;

class NumberOfRelatedItemsUpdateTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'marello_product.number_of_related_items_update';
    }

    public static function getDescription(): string
    {
        return 'Update Related Items (Related/Up-sell/Cross-sell) from Products';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined([
                'max_number_of_related_items',
                'related_entity_class'
            ])
            ->addAllowedTypes('max_number_of_related_items', ['int'])
            ->addAllowedTypes('related_entity_class', ['string']);
    }
}
