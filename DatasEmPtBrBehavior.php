<?php
/**
 * by: George Tavares Lopes
 * george.tavares.lopes@gmail.com
 * https://github.com/georgezim85/yii2-individual-attributes-behavior
 */

namespace app\behaviors;

use yii\db\BaseActiveRecord;

class DatasEmPtBrBehavior extends IndividualAttributesBehavior {

    public $campos = null;
    public $value;

    public function init() {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = $this->gerarArrayDeAtributos($this->campos);
        }
    }

    protected function gerarArrayDeAtributos($campos) {
        $arrayDeAtributos = [];
        foreach ($campos as $campo) {
            array_push($arrayDeAtributos, $campo);
        }
        return [BaseActiveRecord::EVENT_AFTER_FIND => $arrayDeAtributos];
    }

    protected function getIndividualValue($event, $attribute) {
        $convertido = strtotime($this->owner->$attribute);
        if (($this->owner->$attribute != null) && ($convertido !== false)) {
            return date('d/m/Y', strtotime($this->owner->$attribute));
        }
        return parent::getValue($event);
    }

}
