<?php

namespace pine\yii\data;

use pine\yii\data\EnumInterface;

/**
 * Class Enum
 * @package pine\yii\data
 */
abstract class Enum implements EnumInterface
{
    /**
     * Label
     *
     * @param int $value
     * @return null|string
     */
    public static function label($value)
    {
        $valueArray = static::toArray();
        return isset($valueArray[$value]) ? $valueArray[$value] : NULL;
    }
}
