<?php

namespace Marello\Bundle\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;

/**
 * @Oro\Config(
 *  defaultValues={
 *      "security"={
 *          "type"="ACL",
 *          "group_name"=""
 *      },
 *      "dataaudit"={
 *          "auditable"=true
 *      }
 *  }
 * )
 */
#[ORM\Table(name: 'marello_tracking_info')]
#[ORM\UniqueConstraint(name: 'marello_tracking_info_shipmentidx', columns: ['shipment_id'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class TrackingInfo implements ExtendEntityInterface
{
    use ExtendEntityTrait;
    use EntityCreatedUpdatedAtTrait;

    /**
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'tracking_url', type: 'string', length: 255, nullable: true)]
    protected $trackingUrl;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'track_trace_url', type: 'string', length: 255, nullable: true)]
    protected $trackTraceUrl;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'tracking_code', type: 'string', length: 255, nullable: true)]
    protected $trackingCode;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'provider', type: 'string', length: 255, nullable: true)]
    protected $provider;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'provider_name', type: 'string', length: 255, nullable: true)]
    protected $providerName;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var Shipment
     */
    #[ORM\JoinColumn(name: 'shipment_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\OneToOne(targetEntity: \Marello\Bundle\ShippingBundle\Entity\Shipment::class, inversedBy: 'trackingInfo', cascade: ['persist'])]
    protected $shipment;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTrackingUrl(): ?string
    {
        return $this->trackingUrl;
    }

    /**
     * @param string $trackingUrl
     * @return $this
     */
    public function setTrackingUrl(string $trackingUrl): self
    {
        $this->trackingUrl = $trackingUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrackTraceUrl(): ?string
    {
        return $this->trackTraceUrl;
    }

    /**
     * @param string $trackTraceUrl
     * @return $this
     */
    public function setTrackTraceUrl(string $trackTraceUrl): self
    {
        $this->trackTraceUrl = $trackTraceUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrackingCode(): ?string
    {
        return $this->trackingCode;
    }

    /**
     * @param string $trackingCode
     * @return $this
     */
    public function setTrackingCode(string $trackingCode): self
    {
        $this->trackingCode = $trackingCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     * @return $this
     */
    public function setProvider(string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProviderName(): ?string
    {
        return $this->providerName;
    }

    /**
     * @param string $providerName
     * @return $this
     */
    public function setProviderName(string $providerName): self
    {
        $this->providerName = $providerName;

        return $this;
    }

    /**
     * @return Shipment|null
     */
    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    /**
     * @param Shipment $shipment
     * @return $this
     */
    public function setShipment(Shipment $shipment): self
    {
        $this->shipment = $shipment;
        $this->shipment->setTrackingInfo($this);

        return $this;
    }
}
