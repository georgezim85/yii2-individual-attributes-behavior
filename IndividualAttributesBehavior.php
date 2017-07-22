<?php
/**
 * by: George Tavares Lopes
 * george.tavares.lopes@gmail.com
 * https://github.com/georgezim85/yii2-individual-attributes-behavior
 */

namespace app\behaviors;

use yii\db\ActiveRecord;

use yii\behaviors\AttributeBehavior;

class IndividualAttributesBehavior extends AttributeBehavior {

    public function evaluateAttributes($event) {
        if ($this->skipUpdateOnClean && $event->name == ActiveRecord::EVENT_BEFORE_UPDATE && empty($this->owner->dirtyAttributes)
        ) {
            return;
        }

        if (!empty($this->attributes[$event->name])) {
            $attributes = (array) $this->attributes[$event->name];
            foreach ($attributes as $attribute) {
                if (is_string($attribute)) {
                    $this->owner->$attribute = $this->getIndividualValue($event, $attribute);
                }
            }
        }
    }

}
