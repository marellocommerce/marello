<?php

namespace Marello\Bundle\LocaleBundle\Formatter;

use Oro\Bundle\LocaleBundle\Formatter\DateTimeFormatter as BaseDateTimeFormatter;

/**
 * Format dates based on locale settings
 */
class DateTimeFormatter extends BaseDateTimeFormatter
{

    /**
     * Gets instance of intl date formatter by parameters
     *
     * @param string|int|null $dateType
     * @param string|int|null $timeType
     * @param string|null     $locale
     * @param string|null     $timeZone
     * @param string|null     $pattern
     * @param string|null     $value
     *
     * @return \IntlDateFormatter
     */
    protected function getFormatter($dateType, $timeType, $locale, $timeZone, $pattern, $value = null)
    {
        if (!$pattern) {
            $pattern = $this->getPattern($dateType, $timeType, $locale, $value);
        }

        $key = md5(serialize([$timeZone, $pattern]));
        if (!isset($this->cachedFormatters[$key])) {
            $this->cachedFormatters[$key] = new \IntlDateFormatter(
                $locale ?? $this->localeSettings->getLanguage(),
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::NONE,
                $timeZone,
                \IntlDateFormatter::GREGORIAN,
                $pattern
            );
        }

        return $this->cachedFormatters[$key];
    }
}
