<?php

namespace pine\yii\data;

use pine\yii\data\EnumInterface;

abstract class Enum implements EnumInterface
{
    public static function label($value)
    {
        $valueArray = static::toArray();
        return isset($valueArray[$value]) ? $valueArray[$value] : NULL;
    }
}
