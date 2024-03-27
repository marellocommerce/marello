<?php

namespace Marello\Bundle\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="marello_ticket")
 * @Config(
 *      routeName="marello_ticket_index",
 *      defaultValues={
 *            "security"={
 *                "type"="ACL",
 *                "group_name"="",
 *                "category"="account_management"
 *            }
 *      }
 *  )
 */
class Ticket implements ExtendEntityInterface
{
    use ExtendEntityTrait;
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Marello\Bundle\CustomerBundle\Entity\Customer"
     * )
     * @ORM\JoinColumn(
     *     name="customer_id",
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    private $customer;

    /**
     * @ORM\Column(
     *     name="firstName",
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(
     *     name="lastName",
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(
     *     name="email",
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    private $email;

    /**
     * @ORM\Column(
     *     name="phone",
     *     type="string",
     *     length=255,
     *     nullable=true
     * )
     */
    private $phone;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Oro\Bundle\UserBundle\Entity\User"
     * )
     * @ORM\JoinColumn(
     *     name="owner_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $owner;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Oro\Bundle\UserBundle\Entity\User"
     * )
     * @ORM\JoinColumn(
     *     name="assigned_to_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    private $assignedTo;

    /**
     * @var \Extend\Entity\EV_Marello_Ticket_Priority
     */
    private $priority;

    /**
     * @var \Extend\Entity\EV_Marello_Ticket_Source
     */
    private $source;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Marello\Bundle\TicketBundle\Entity\TicketCategoryType"
     * )
     * @ORM\JoinColumn(
     *     name="category_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $category;

    /**
     * @ORM\Column(
     *     name="subject",
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */

    private $subject;

    /**
     * @ORM\Column(
     *     name="description",
     *     type="string",
     *     length=1000,
     *     nullable=false
     * )
     */
    private $description;

    /**
     * @ORM\Column(
     *     name="resolution",
     *     type="string",
     *     length=1000,
     *     nullable=true
     * )
     */
    private $resolution;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return string
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * @param string $resolution
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return TicketCategoryType
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     * @param mixed $assignedTo
     */
    public function setAssignedTo($assignedTo): void
    {
        $this->assignedTo = $assignedTo;
    }
//
//    public function getSource(): \Extend\Entity\EV_Marello_Ticket_Source
//    {
//        return $this->source;
//    }
//
//    public function setSource(\Extend\Entity\EV_Marello_Ticket_Source $source): void
//    {
//        $this->source = $source;
//    }
//
//    public function getPriority(): \Extend\Entity\EV_Marello_Ticket_Priority
//    {
//        return $this->priority;
//    }
//
//    public function setPriority(\Extend\Entity\EV_Marello_Ticket_Priority $priority): void
//    {
//        $this->priority = $priority;
//    }

}