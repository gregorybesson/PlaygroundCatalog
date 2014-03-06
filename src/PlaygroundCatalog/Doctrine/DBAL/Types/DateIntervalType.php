<?php

namespace PlaygroundCatalog\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\ConversionException;
use Herrera\DateInterval\DateInterval;

class DateIntervalType extends BigIntType
{
    /**
     * The DateInterval type name.
     *
     * @var string
     */
    const DATEINTERVAL = 'dateinterval';

    /**
     * @override
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $interval = new DateInterval('PT0S');
        return (null === $value) ? null : $interval->toSeconds($value);
    }

    /**
     * @override
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null !== $value) {
            if (false == preg_match('/^\d+$/', $value)) {
                throw ConversionException::conversionFailedFormat(
                    $value,
                    $this->getName(),
                    '^\\d+$'
                );
            }

            $value = DateInterval::fromSeconds($value);
        }

        return $value;
    }

    /**
     * @override
     */
    public function getName()
    {
        return self::DATEINTERVAL;
    }
}