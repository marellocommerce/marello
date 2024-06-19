<?php

namespace Marello\Bundle\LocaleBundle\Manager;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Util\ClassUtils;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EmailBundle\Entity\EmailTemplate;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EmailBundle\Model\EmailTemplateCriteria;
use Oro\Bundle\EmailBundle\Model\EmailTemplate as EmailTemplateModel;

use Marello\Bundle\LocaleBundle\Model\LocalizationAwareInterface;

class EmailTemplateManager
{
    /**
     * @param DoctrineHelper $doctrineHelper
     * @param ConfigManager $configManager
     */
    public function __construct(
        protected DoctrineHelper $doctrineHelper,
        protected ConfigManager $configManager
    ) {
    }

    /**
     * @param $templateName
     * @param $entity
     * @return \Extend\Entity\EX_OroEmailBundle_EmailTemplate|null|EmailTemplate
     */
    public function findTemplate($templateName, $entity)
    {
        $template = $this->findEntityLocalizationTemplate($templateName, $entity);

        /*
         * If translation not found or not supported, try to get default template.
         */
        if ($template === null) {
            $template = $this->findDefaultTemplate($templateName, $entity);
        }
        
        return $template;
    }

    /**
     * @param $templateName
     * @param $entity
     * @return null|object|EmailTemplate
     */
    public function findEntityLocalizationTemplate($templateName, $entity)
    {
        if ($entity instanceof LocalizationAwareInterface) {
            $entityName = ClassUtils::getRealClass(get_class($entity));
            $criteria = new EmailTemplateCriteria($templateName, $entityName);
            $emailTemplate = $this
                ->getEmailTemplateRepository()
                ->findWithLocalizations($criteria);

            return $emailTemplate;
        }

        return null;
    }

    /**
     * @param $templateName
     * @param $entity
     * @return null|object|EmailTemplate
     */
    protected function findDefaultTemplate($templateName, $entity)
    {
        $entityName = ClassUtils::getRealClass(get_class($entity));

        return $this
            ->getEmailTemplateRepository()
            ->findOneBy(['name' => $templateName, 'entityName' => $entityName]);
    }

    /**
     * Get email template repository to find localized versions of templates
     * @return EntityRepository
     */
    private function getEmailTemplateRepository()
    {
        return $this->doctrineHelper
            ->getEntityRepository(EmailTemplate::class);
    }

    /**
     * @param EmailTemplate $template
     * @param $entity
     * @return null|EmailTemplateModel
     */
    public function getLocalizedModel(EmailTemplate $template, $entity)
    {
        return null;
    }
}
