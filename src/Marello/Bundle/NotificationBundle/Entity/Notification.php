<?php

namespace Marello\Bundle\NotificationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\ActivityBundle\Model\ActivityInterface;
use Oro\Bundle\ActivityBundle\Model\ExtendActivity;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\EmailBundle\Entity\EmailTemplate;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

#[ORM\Table(name: 'marello_notification')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(defaultValues: ['grouping' => ['groups' => ['activity']], 'ownership' => ['organization_field_name' => 'organization', 'organization_column_name' => 'organization_id'], 'security' => ['type' => 'ACL', 'group_name' => '']])]
class Notification implements ActivityInterface, ExtendEntityInterface
{
    use ExtendActivity;
    use ExtendEntityTrait;

    /**
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     *
     * @var EmailTemplate
     */
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Oro\Bundle\EmailBundle\Entity\EmailTemplate::class, cascade: [])]
    protected $template;

    /**
     * @var array
     */
    #[ORM\Column(name: 'recipients', type: Types::JSON, nullable: false)]
    protected $recipients;

    /**
     * @var string
     */
    #[ORM\Column(name: 'body', type: Types::TEXT)]
    protected $body;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    #[Oro\ConfigField(defaultValues: ['entity' => ['label' => 'oro.ui.created_at']])]
    protected $createdAt;

    /**
     * @var Organization
     */
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \Oro\Bundle\OrganizationBundle\Entity\Organization::class)]
    protected $organization;

    /**
     * @var Collection|File
     */
    #[ORM\JoinTable(name: 'marello_notification_attach')]
    #[ORM\JoinColumn(name: 'notification_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'attachment_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: \Oro\Bundle\AttachmentBundle\Entity\Attachment::class, cascade: ['all'])]
    protected $attachments;

    /**
     * Notification constructor.
     *
     * @param EmailTemplate $template
     * @param array         $recipients
     * @param string        $body
     * @param Organization  $organization
     */
    public function __construct(EmailTemplate $template, array $recipients, $body, Organization $organization)
    {
        $this->template     = $template;
        $this->recipients   = $recipients;
        $this->organization = $organization;
        $this->body         = $body;
        $this->attachments  = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->createdAt = new \DateTime('now', new \DateTimeZone('UTC'));
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
     * @return Organization
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

    /**
     * @return Collection|File
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param File $attachment
     * @return $this
     */
    public function addAttachment(File $attachment)
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
        }

        return $this;
    }

    /**
     * @param File $attachment
     * @return $this
     */
    public function removeAttachment(File $attachment)
    {
        $this->attachments->removeElement($attachment);

        return $this;
    }
}
