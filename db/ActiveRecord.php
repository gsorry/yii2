<?php

namespace pine\yii\db;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use pine\yii\behaviors\DeleteBehavior;
use pine\yii\data\BooleanEnum;

/**
 * Class ActiveRecord
 * @package pine\yii\db
 */
class ActiveRecord extends \yii\db\ActiveRecord
{

    /**
     * @var \common\models\User
     */
    private $createdBy = NULL;

    /**
     * @var \common\models\User
     */
    private $updatedBy = NULL;

    /**
     * Behaviors
     *
     * @return array
     */
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

    /**
     * Get Created By
     *
     * @return \common\models\User
     */
    public function getCreatedBy()
    {
        if (NULL === $this->createdBy) {
            $this->createdBy = \common\models\User::findOne(['id'=>$this->created_by]);
        }
        return $this->createdBy;
    }

    /**
     * Get Updated By
     *
     * @return \common\models\User
     */
    public function getUpdatedBy()
    {
        if (NULL === $this->updatedBy) {
            $this->updatedBy = \common\models\User::findOne(['id'=>$this->updated_by]);
        }
        return $this->updatedBy;
    }

    /**
     * Get Deleted Label
     *
     * @return null|string
     */
    public function getDeletedLabel()
    {
        return BooleanEnum::label($this->deleted);
    }

    /**
     * Get Hidden Label
     *
     * @return null|string
     */
    public function getHiddenLabel()
    {
        return BooleanEnum::label($this->hidden);
    }

    /**
     * Find One
     *
     * @param $condition
     * @return \pine\yii\db\ActiveRecord
     */
    public static function findOne($condition)
    {
        return static::findByCondition($condition)->andWhere(static::tableName().'.deleted=0')->one();
    }

}