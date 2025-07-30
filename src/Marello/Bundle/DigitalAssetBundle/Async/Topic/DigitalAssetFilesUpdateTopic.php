<?php

namespace Marello\Bundle\DigitalAssetBundle\Async\Topic;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Component\MessageQueue\Topic\AbstractTopic;

class DigitalAssetFilesUpdateTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'marello_digital_asset.source_files_update';
    }

    public static function getDescription(): string
    {
        return 'Update digital asset files with external url';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined([
                'digitalAssetId',
            ])
            ->setRequired([
                'digitalAssetId',
            ])
            ->addAllowedTypes('digitalAssetId', ['int']);
    }
}