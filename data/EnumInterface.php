<?php

namespace pine\yii\data;

interface EnumInterface
{
    public static function toArray();
    public static function label($value);
}
