<?php

namespace pine\yii\data;

/**
 * Interface EnumInterface
 * @package pine\yii\data
 */
interface EnumInterface
{
    /**
     * To Array
     *
     * @return array
     */
    public static function toArray();

    /**
     * Label
     *
     * @param int $value
     * @return null|string
     */
    public static function label($value);
}
