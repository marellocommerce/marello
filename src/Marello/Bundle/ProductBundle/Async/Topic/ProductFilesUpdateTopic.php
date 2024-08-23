<?php

namespace Marello\Bundle\ProductBundle\Async\Topic;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Component\MessageQueue\Topic\AbstractTopic;

class ProductFilesUpdateTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'marello_product.product_files_update';
    }

    public static function getDescription(): string
    {
        return 'Update product files with external url';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined([
                'productId',
            ])
            ->setRequired([
                'productId',
            ])
            ->addAllowedTypes('productId', ['int']);
    }
}
