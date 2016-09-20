<?php


namespace Marello\Component\Pricing\ORM\Type;

use Oro\DBAL\Types\MoneyType as OroMoneyType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Brick\Math\BigDecimal;

class MoneyType extends OroMoneyType
{
    const TYPE_PRECISION = 21;
    const TYPE_SCALE     = 6;

    /**
     * @param BigDecimal $value
     * @param AbstractPlatform $platform
     * @return BigDecimal
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return BigDecimal::of($value);
    }

    /**
     * @param BigDecimal $value
     * @param AbstractPlatform $platform
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof BigDecimal) {
            return (string) $value;
        }

        return $value;
    }
}
