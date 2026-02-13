<?php

namespace Marello\Bundle\CoreBundle\Async\Topic;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Component\MessageQueue\Topic\AbstractTopic;

class SequenceNumberEntityCreationTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'marello_core.sequence_number_entity_create';
    }

    public static function getDescription(): string
    {
        return 'Create Number Sequence entity';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined([
                'organizationId'
            ])
            ->setRequired([
                'organizationId'
            ])
            ->addAllowedTypes('organizationId', ['int']);
    }
}
