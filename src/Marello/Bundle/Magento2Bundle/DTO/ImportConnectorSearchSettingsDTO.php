<?php

namespace Marello\Bundle\Magento2Bundle\DTO;

class ImportConnectorSearchSettingsDTO
{
    /** @var int */
    public const NO_WEBSITE_ID = 0;

    /** @var \DateTime */
    protected $syncStartDateTime;

    /** @var \DateInterval */
    protected $syncDateInterval;

    /** @var \DateInterval */
    protected $missTimingDateInterval;

    /** @var int */
    protected $websiteId = self::NO_WEBSITE_ID;

    /**
     * @param \DateTime $syncStartDateTime
     * @param \DateInterval $syncDateInterval
     * @param \DateInterval $missTimingDateInterval
     * @param int $websiteId
     */
    public function __construct(
        \DateTime $syncStartDateTime,
        \DateInterval $syncDateInterval,
        \DateInterval $missTimingDateInterval,
        int $websiteId = self::NO_WEBSITE_ID
    ) {
        $this->syncStartDateTime = clone $syncStartDateTime;
        $this->syncDateInterval = clone $syncDateInterval;
        $this->missTimingDateInterval = clone $missTimingDateInterval;
        $this->websiteId = $websiteId;
    }

    /**
     * @param bool $clone
     * @return \DateTime
     */
    public function getSyncStartDateTime(bool $clone = true): \DateTime
    {
        if ($clone) {
            return clone $this->syncStartDateTime;
        }

        return $this->syncStartDateTime;
    }

    /**
     * @return \DateInterval
     */
    public function getSyncDateInterval(): \DateInterval
    {
        return $this->syncDateInterval;
    }

    /**
     * @return \DateInterval
     */
    public function getMissTimingDateInterval(): \DateInterval
    {
        return $this->missTimingDateInterval;
    }

    /**
     * @return int
     */
    public function getWebsiteId(): int
    {
        return $this->websiteId;
    }

    /**
     * @return bool
     */
    public function isNoWebsiteSet(): bool
    {
        return $this->websiteId === self::NO_WEBSITE_ID;
    }
}
