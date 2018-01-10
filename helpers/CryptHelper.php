<?php

namespace pine\yii\helpers;

class CryptHelper
{
    public static function encrypt($data)
    {
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, \Yii::$app->params['saltString'], $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }

    public static function decrypt($data)
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, \Yii::$app->params['saltString'], base64_decode($data), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }
}
