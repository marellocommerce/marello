<?php

namespace Marello\Bundle\CoreBundle\Async\Topic;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Component\MessageQueue\Topic\AbstractTopic;

class FileUpdateTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'marello_core.file_update';
    }

    public static function getDescription(): string
    {
        return 'Update attachment files with external url';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined([
                'entityId',
                'entityClass'
            ])
            ->setRequired([
                'entityId',
                'entityClass'
            ])
            ->addAllowedTypes('entityId', ['int'])
            ->addAllowedTypes('entityClass', ['string']);
    }
}
