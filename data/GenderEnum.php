<?php

namespace pine\yii\data;

use Yii;
use pine\yii\data\Enum;

class GenderEnum extends Enum
{
    const NONE   = 0;
    const MALE   = 1;
    const FEMALE = 2;

    public static function toArray()
    {
        return [
            self::NONE   => Yii::t('app', 'GENDER_NONE'),
            self::MALE   => Yii::t('app', 'GENDER_MALE'),
            self::FEMALE => Yii::t('app', 'GENDER_FEMALE'),
        ];
    }
}
