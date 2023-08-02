<?php


namespace eluhr\notification\models;


use yii\helpers\HtmlPurifier;
use yii\validators\FilterValidator;

class HTMLPurifierFilterValidator extends FilterValidator
{
    public function init () {
        $this->filter = function ($value) {
            if (is_string($value)) {
                $value = HtmlPurifier::process($value);
            }

            return $value;
        };

        parent::init();
    }
}
