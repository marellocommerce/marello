<?php

namespace Marello\Bundle\WebhookBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\WebhookBundle\Entity\Repository\WebhookRepository;

#[ORM\Table(name: 'marello_webhook')]
#[ORM\Index(name: 'idx_marello_webhook_created_at', columns: ['created_at'])]
#[ORM\Index(name: 'idx_marello_webhook_updated_at', columns: ['updated_at'])]
#[ORM\Entity(repositoryClass: WebhookRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(
    defaultValues: [
        'entity' => ['icon' => 'fa-briefcase'],
        'dataaudit' => ['auditable' => true],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'],
        'security' => ['type' => 'ACL', 'group_name' => '']
    ]
)]
class Webhook implements OrganizationAwareInterface, ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'name', type: Types::STRING)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $name;

    /**
     * @var string
     */
    #[ORM\Column(name: 'event', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['excluded' => true]])]
    protected $event;

    /**
     * @var string
     */
    #[ORM\Column(name: 'callback_url', type: Types::STRING)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $callbackUrl;

    /**
     * @var string
     */
    #[ORM\Column(name: 'secret', type: Types::STRING)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $secret;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'enabled', type: Types::BOOLEAN)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $enabled;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Webhook
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Webhook
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param string $event
     * @return Webhook
     */
    public function setEvent($event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string
     */
    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    /**
     * @param string $callbackUrl
     * @return Webhook
     */
    public function setCallbackUrl(string $callbackUrl): self
    {
        $this->callbackUrl = $callbackUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     * @return Webhook
     */
    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return Webhook
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
