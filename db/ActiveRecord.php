<?php

namespace pine\yii\db;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use pine\yii\behaviors\DeleteBehavior;
use pine\yii\data\BooleanEnum;

class ActiveRecord extends \yii\db\ActiveRecord
{

    private $createdBy = NULL;

    private $updatedBy = NULL;

    public function behaviors()
    {
        return [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
            ],
            'BlameableBehavior' => [
                'class' => BlameableBehavior::className(),
            ],
            'DeleteBehavior' => [
                'class' => DeleteBehavior::className(),
            ],
            'HiddenBehavior' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['hidden'],
                ],
                'value' => function() {
                    return 0;
                },
            ],
        ];
    }

    public function getCreatedBy()
    {
        if (NULL === $this->createdBy) {
            $this->createdBy = \common\models\User::findOne(['id'=>$this->created_by]);
        }
        return $this->createdBy;
    }

    public function getUpdatedBy()
    {
        if (NULL === $this->updatedBy) {
            $this->updatedBy = \common\models\User::findOne(['id'=>$this->updated_by]);
        }
        return $this->updatedBy;
    }

    public function getDeletedLabel()
    {
        return BooleanEnum::label($this->deleted);
    }

    public function getHiddenLabel()
    {
        return BooleanEnum::label($this->hidden);
    }

    public static function findOne($condition)
    {
        return static::findByCondition($condition)->andWhere(static::tableName().'.deleted=0')->one();
    }

}