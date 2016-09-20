<?php

namespace Marello\Bundle\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\NotificationBundle\Model\ExtendNotification;
use Oro\Bundle\EmailBundle\Entity\EmailTemplate;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\NotificationBundle\Processor\EmailNotificationInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;

/**
 * @Oro\Config(
 *  defaultValues={
 *      "grouping"={"groups"={"activity"}},
 *      "ownership"={
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *      },
 *      "security"={
 *          "type"="ACL",
 *          "group_name"=""
 *      },
 *  }
 * )
 */
class Notification extends ExtendNotification implements EmailNotificationInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var EmailTemplate
     */
    protected $template;

    /**
     * @var array
     */
    protected $recipients;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var OrganizationInterface
     */
    protected $organization;

    /**
     * Notification constructor.
     *
     * @param EmailTemplate         $template
     * @param array                 $recipients
     * @param string                $body
     * @param OrganizationInterface $organization
     */
    public function __construct(EmailTemplate $template, array $recipients, $body, OrganizationInterface $organization)
    {
        parent::__construct();

        $this->template     = $template;
        $this->recipients   = $recipients;
        $this->organization = $organization;
        $this->body         = $body;
    }

    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Gets a template can be used to prepare a notification message
     *
     * @return EmailTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Gets a list of email addresses can be used to send a notification message
     *
     * @return string[]
     */
    public function getRecipientEmails()
    {
        return $this->recipients;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return OrganizationInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}
