<?php

namespace Marello\Bundle\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ticket_category_type")
 * @Config(
 *     routeName="ticket_category_type_index",
 *     defaultValues={
 *           "security"={
 *               "type"="ACL",
 *               "group_name"="",
 *               "category"="account_management"
 *           }
 *     }
 * )
 */
class TicketCategoryType implements ExtendEntityInterface
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
     * @ORM\Column(name="category", type="string",length=255, nullable=false)
     * @ConfigField(
     *       defaultValues={
     *           "importexport"={
     *               "order"=20
     *           }
     *       }
     *  )
     */
    protected $category;

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }
}