<?php

namespace Marello\Bundle\PurchaseOrderBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;

use Oro\Bundle\EmailBundle\Entity\EmailTemplate;
use Oro\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;
use Oro\Bundle\EmailBundle\Migrations\Data\ORM\AbstractEmailFixture;

class UpdateEmailTemplatesHtmlEscapeTags extends AbstractEmailFixture implements VersionedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $emailTemplates = $this->getEmailTemplatesList($this->getEmailsDir());

        foreach ($emailTemplates as $fileName => $file) {
            $this->loadTemplate($manager, $fileName, $file);
        }

        $template = $this->findExistingTemplate(
            $manager,
            [
                'params' => [
                    'name' => 'marello_purchase_order_model_advise'
                ]
            ]
        );
        if ($template) {
            $manager->remove($template);
        }
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function findExistingTemplate(ObjectManager $manager, array $template)
    {
        $name = $template['params']['name'];
        if (empty($name)) {
            return null;
        }

        return $manager->getRepository(EmailTemplate::class)->findOneBy([
            'name' => $template['params']['name']
        ]);
    }

    /**
     * Return path to email templates
     *
     * @return string
     */
    public function getEmailsDir()
    {
        return $this->container
            ->get('kernel')
            ->locateResource('@MarelloPurchaseOrderBundle/Migrations/Data/ORM/data/emails');
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '1.4';
    }
}
