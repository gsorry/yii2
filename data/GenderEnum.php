<?php

namespace pine\yii\data;

use Yii;
use pine\yii\data\Enum;

/**
 * Class GenderEnum
 * @package pine\yii\data
 */
class GenderEnum extends Enum
{
    const NONE   = 0;
    const MALE   = 1;
    const FEMALE = 2;

    /**
     * To Array
     *
     * @return array
     */
    public static function toArray()
    {
        return [
            self::NONE   => Yii::t('app', 'GENDER_NONE'),
            self::MALE   => Yii::t('app', 'GENDER_MALE'),
            self::FEMALE => Yii::t('app', 'GENDER_FEMALE'),
        ];
    }
}
