<?php

namespace pine\yii\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class Select2Behavior
 * @package pine\yii\behaviors
 */
class Select2Behavior extends \yii\base\Behavior
{
    /**
     * @var array
     */
    public $attributes = [];

    /**
     * @var string
     */
    public $from = 'id';

    /**
     * @var string
     */
    public $to = 'name';

    /**
     * Events
     *
     * @return array
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'eventAfterFind',
        ];
    }

    /**
     * Event After Find
     */
    public function eventAfterFind()
    {
        if (count($this->attributes) > 0) {
            foreach ($this->attributes as $attribute => $relation) {
                $relationMethod = 'get'.ucfirst($relation);
                $this->owner->{$attribute} = implode(',', ArrayHelper::map($this->owner->{$relation}, $this->from, $this->to));
            }
        }
    }

}