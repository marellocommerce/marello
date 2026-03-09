<?php

namespace Marello\Bundle\CoreBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Oro\Bundle\EmailBundle\Entity\EmailTemplate;

use Marello\Bundle\OrderBundle\Migrations\Data\ORM\LoadEmailTemplatesData;

class UpdateEmailTemplateNames extends AbstractFixture implements
    DependentFixtureInterface
{

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            LoadEmailTemplatesData::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $emailTemplates = $this->manager->getRepository(EmailTemplate::class)->findAll();
        foreach ($emailTemplates as $emailTemplate) {
            $emailTemplate->setTemplateName($emailTemplate->getName());
            $this->manager->persist($emailTemplate);
        }
        $this->manager->flush();
    }
}
