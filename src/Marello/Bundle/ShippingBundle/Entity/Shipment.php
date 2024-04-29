<?php

namespace Marello\Bundle\ShippingBundle\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

#[ORM\Entity, ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_shipment')]
#[ORM\UniqueConstraint(name: 'marello_shipment_trackinginfoidx', columns: ['tracking_info_id'])]
#[Oro\Config(
    defaultValues: [
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => ''],
    ]
)]
class Shipment implements DatesAwareInterface, OrganizationAwareInterface, ExtendEntityInterface
{
    use DatesAwareTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'shipping_service', type: Types::STRING, length:255, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?string $shippingService = null;

    #[ORM\Column(name: 'ups_shipment_digest', type: Types::TEXT, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?string $upsShipmentDigest = null;

    #[ORM\Column(name: 'identification_number', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?string $identificationNumber = null;

    #[ORM\Column(name: 'ups_package_tracking_number', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?string $upsPackageTrackingNumber = null;

    #[ORM\Column(name: 'base64_encoded_label', type: Types::TEXT, length: 255, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?string $base64EncodedLabel = null;

    #[ORM\OneToOne(mappedBy: 'shipment', targetEntity: TrackingInfo::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'tracking_info_id', referencedColumnName: 'id', nullable: true)]
    #[Oro\ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true]
    ])]
    protected ?TrackingInfo $trackingInfo = null;

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
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getShippingService(): ?string
    {
        return $this->shippingService;
    }

    /**
     * @param string $shippingService
     *
     * @return $this
     */
    public function setShippingService(string $shippingService = null): self
    {
        $this->shippingService = $shippingService;

        return $this;
    }

    /**
     * @return string
     */
    public function getUpsShipmentDigest(): ?string
    {
        return $this->upsShipmentDigest;
    }

    /**
     * @param string $upsShipmentDigest
     *
     * @return $this
     */
    public function setUpsShipmentDigest(string $upsShipmentDigest = null): self
    {
        $this->upsShipmentDigest = $upsShipmentDigest;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentificationNumber(): ?string
    {
        return $this->identificationNumber;
    }

    /**
     * @param string $identificationNumber
     *
     * @return $this
     */
    public function setIdentificationNumber(string $identificationNumber = null): self
    {
        $this->identificationNumber = $identificationNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getUpsPackageTrackingNumber(): ?string
    {
        return $this->upsPackageTrackingNumber;
    }

    /**
     * @param string $upsPackageTrackingNumber
     *
     * @return $this
     */
    public function setUpsPackageTrackingNumber(string $upsPackageTrackingNumber = null): self
    {
        $this->upsPackageTrackingNumber = $upsPackageTrackingNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getBase64EncodedLabel(): ?string
    {
        return $this->base64EncodedLabel;
    }

    /**
     * @param string $base64EncodedLabel
     *
     * @return $this
     */
    public function setBase64EncodedLabel(string $base64EncodedLabel = null): self
    {
        $this->base64EncodedLabel = $base64EncodedLabel;

        return $this;
    }

    /**
     * @return TrackingInfo|null
     */
    public function getTrackingInfo(): ?TrackingInfo
    {
        return $this->trackingInfo;
    }

    /**
     * @param TrackingInfo|null $trackingInfo
     *
     * @return $this
     */
    public function setTrackingInfo(TrackingInfo $trackingInfo = null): self
    {
        $this->trackingInfo = $trackingInfo;

        return $this;
    }
}
