<?php

namespace Marello\Bundle\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ticket_source_type")
 * @Config(
 *     routeName="ticket_source_type_index",
 *     defaultValues={
 *           "security"={
 *               "type"="ACL",
 *               "group_name"="",
 *               "category"="account_management"
 *           }
 *     }
 * )
 */
class TicketSourceType implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ConfigField(
     *        defaultValues={
     *            "importexport"={
     *                "order"=10
     *            }
     *        }
     *   )
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string",length=255, nullable=false)
     * @ConfigField(
     *       defaultValues={
     *           "importexport"={
     *               "order"=20
     *           }
     *       }
     *  )
     */
    protected $source;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }
}