<?php

namespace pine\yii\data;

use Yii;
use pine\yii\data\Enum;

class BooleanEnum extends Enum
{
    const BOOLEAN_NO  = 0;
    const BOOLEAN_YES = 1;

    public static function toArray()
    {
        return [
            self::BOOLEAN_NO  => Yii::t('app', 'BOOLEAN_NO'),
            self::BOOLEAN_YES => Yii::t('app', 'BOOLEAN_YES'),
        ];
    }
}
