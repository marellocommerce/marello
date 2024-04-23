<?php

namespace Marello\Bundle\RuleBundle\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

/**
 *
 */
#[ORM\Entity, ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_rule')]
#[ORM\Index(columns: ['created_at'], name: 'idx_marello_rule_created_at')]
#[ORM\Index(columns: ['updated_at'], name: 'idx_marello_rule_updated_at')]
#[Oro\Config(
   defaultValues: [
       'entity' => [
           'icon' => 'fa-briefcase'
      ],
       'dataaudit' => [
           'auditable' => true
      ],
       'security' => [
           'type' => 'ACL',
           'group_name' => ''
      ]
   ]
)]
class Rule implements DatesAwareInterface, RuleInterface, ExtendEntityInterface
{
    use DatesAwareTrait;
    use ExtendEntityTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['identity' => true, 'order' => 10]]
    )]
    private ?string $name = null;

    #[ORM\Column(name: 'enabled', type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 20]])]
    private ?bool $enabled = true;

    #[ORM\Column(name: 'sort_order', type: Types::INTEGER)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 30]])]
    private ?int $sortOrder = null;

    #[ORM\Column(name: 'stop_processing', type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 40]])]
    private ?bool $stopProcessing = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 50]])]
    private ?string $expression = null;

    #[ORM\Column(name: 'is_system', type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 50]])]
    private ?bool $system = false;

    #[ORM\PrePersist]
    public function prePersist()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->setCreatedAt($now);
        $this->setUpdatedAt($now);
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStopProcessing(): bool
    {
        return $this->stopProcessing;
    }

    /**
     * @param bool $stopProcessing
     * @return $this
     */
    public function setStopProcessing(bool $stopProcessing): self
    {
        $this->stopProcessing = $stopProcessing;

        return $this;
    }
    
    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @param string $expression
     * @return $this
     */
    public function setExpression(string $expression): self
    {
        $this->expression = $expression;

        return $this;
    }
    
    /**
     * @param bool $system
     * @return $this
     */
    public function setSystem(bool $system): self
    {
        $this->system = $system;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSystem(): bool
    {
        return $this->system;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->name;
    }
}
