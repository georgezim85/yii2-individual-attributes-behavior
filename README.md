# yii2-individual-attributes-behavior
A class that you can extend when need to set individual value for each event's attribute.

Uma classe que você pode estender quando precisar setar valores individuais para cada atributo relacionado ao evento.



I just overwrited one function of Yii's AttributeBehavior, and it worked nice:

Eu apenas sobrescrevi uma função da classe AttributeBehavior do Yii, e funcionou perfeitamente:



AttributeBehavior's funcion:

Função do AttributeBehavior sobrescrita:

```php
    public function evaluateAttributes($event)
    {
        if ($this->skipUpdateOnClean
            && $event->name == ActiveRecord::EVENT_BEFORE_UPDATE
            && empty($this->owner->dirtyAttributes)
        ) {
            return;
        }

        if (!empty($this->attributes[$event->name])) {
            $attributes = (array) $this->attributes[$event->name];
            $value = $this->getValue($event);
            foreach ($attributes as $attribute) {
                // ignore attribute names which are not string (e.g. when set by TimestampBehavior::updatedAtAttribute)
                if (is_string($attribute)) {
                    $this->owner->$attribute = $value;
                }
            }
        }
    }
```

IndividualAttributesBehavior's function that overwrited the one above:

Função do IndividualAttributesBehavior que sobrescreveu a de cima:

```php
    public function evaluateAttributes($event) 
    {
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
```

So I created the getIndividualValue function on my customized date formatter behavior that extends from IndividualAttributesBehavior:


Então eu criei a função getIndividualValue no meu manipulador customizado de datas, que estende de IndividualAttributesBehavior:

```php
<?php

namespace app\behaviors;

use yii\db\BaseActiveRecord;

/**
 * Converts date fields to pt-br format on EVENT_AFTER_FIND
 * Converte campos de data para formato pt-br no evento EVENT_AFTER_FIND
**/
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
```

So, on my models' behavior functions:

Ento, no behavior do meu model:

```php
    public function behaviors() 
    {
        return [
            [
                'class' => DatasEmPtBrBehavior::className(),
                'campos' => [ // Array of fields that will have different values
                    'created', 
                    'updated',
                    'date_start'
                ]
            ]
        ];
    }
```
