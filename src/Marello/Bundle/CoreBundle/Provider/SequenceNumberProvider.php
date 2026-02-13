<?php

namespace Marello\Bundle\CoreBundle\Provider;

use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\MaintenanceBundle\Maintenance\Mode as MaintenanceMode;
use Oro\Bundle\SecurityBundle\Metadata\EntitySecurityMetadataProvider;
use Oro\Bundle\EntityExtendBundle\Extend\EntityExtendUpdateHandlerInterface;

class SequenceNumberProvider
{
    public function __construct(
        protected ConfigManager $configManager,
        protected EntityExtendUpdateHandlerInterface $entityExtendUpdateHandler,
        private MaintenanceMode $maintenance
    ) {}

    /**
     *  Name generated for the Sequence number for per type and organisation
     *  Marello<Type>Sequence_<OrgId> --> MarelloOrderSequence_1
     * @param string $sequenceEntityType
     * @param int $organisationId
     * @return string
     */
    public static function generateSequenceEntityName(string $sequenceEntityType, int $organisationId)
    {
        return sprintf('Marello%sSequence_%d', ucfirst($sequenceEntityType), $organisationId);
    }

    /**
     * @param string $sequenceEntityName
     * @return object
     */
    public static function generateSequenceEntity(string $sequenceEntityName): object
    {
        $extendEntity = ExtendHelper::ENTITY_NAMESPACE . $sequenceEntityName;
        return new $extendEntity();
    }

    public function generateNewSequenceEntityAndTable($sequenceEntityType, $organisationId, $update = true)
    {
        $entityName = SequenceNumberProvider::generateSequenceEntityName($sequenceEntityType, $organisationId);
        $className = ExtendHelper::ENTITY_NAMESPACE . $entityName;

        $entityModel = $this->configManager->createConfigEntityModel($className);
        $extendConfig = $this->configManager->getProvider('extend')->getConfig($className);
        $extendConfig->set('owner', ExtendScope::OWNER_CUSTOM);
        $extendConfig->set('state', ExtendScope::STATE_NEW);
        $extendConfig->set('upgradeable', true);
        $extendConfig->set('is_extend', true);

        $config = $this->configManager->getProvider('security')->getConfig($className);
        $config->set('type', EntitySecurityMetadataProvider::ACL_SECURITY_TYPE);

        $this->configManager->persist($extendConfig);
        $this->configManager->flush();
        if ($update) {
            $result = $this->entityExtendUpdateHandler->update();
            if (!$result->isSuccessful()) {

                // maybe create a notification here when the creation fails
            } else {
                $this->maintenance->off();
            }
        }
    }
}
