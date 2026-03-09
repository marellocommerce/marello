<?php

namespace Marello\Bundle\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EmailBundle\Entity\EmailTemplate;
use Oro\Bundle\EmailBundle\Entity\Repository\EmailTemplateRepository;

class EmailTemplateSelectType extends AbstractType
{
    public function __construct(
        protected DoctrineHelper $doctrineHelper
    ) {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('entityName')
            ->setDefault('choices', function (Options $options) {
                return $this->getEmailTemplates($options['entityName']);
            })
            ->setDefaults([
                'required' => false,
            ])
        ;
    }

    /**
     * @param string $entityName full qualified class name
     * @return array
     */
    protected function getEmailTemplates($entityName)
    {
        $choices = [];
        /** @var EmailTemplateRepository $emailTemplateRepo */
        $emailTemplateRepo = $this->doctrineHelper->getEntityRepositoryForClass(EmailTemplate::class);
        $qb = $emailTemplateRepo->createQueryBuilder('et')
            ->where('et.entityName = :entityName')
            ->orderBy('et.name', 'ASC')
            ->andWhere('et.isSystem = :isSystem')
            ->setParameter('entityName', $entityName)
            ->setParameter('isSystem', false);

        $result = $qb->getQuery()->getResult();
        foreach ($result as $emailTemplate) {
            if (null === $emailTemplate->getTemplateName()) {
                continue;
            }
            $choices[$emailTemplate->getTemplateName()] = $emailTemplate->getName();
        }

        return $choices;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
