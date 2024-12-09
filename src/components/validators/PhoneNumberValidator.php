<?php

namespace vs\yii2\auth\components\validators;

use \yii\validators\Validator;

class PhoneNumberValidator extends Validator
{
    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $value = preg_replace('/\D/', '', $value);

        $result = $this->validateValue($value);
        if (!empty($result)) {
            $this->addError($model, $attribute, $result[0], $result[1]);
        }
    }

    /**
     * @param string $value
     * @return array|null
     */
    public function validateValue($value)
    {
        if ($this->isEmpty($value)) {
            return [
                \Yii::t('yii', 'Необходимо заполнить «{attribute}».'),
                []
            ];
        }

        if ($this->startsWithValidDigit($value) === false || $this->hasValidLength($value) === false) {
            return [
                \Yii::t('yii', 'Телефонный номер должен начинаться с +7 или 8 и содержать 11 цифр.'),
                []
            ];
        }

        return null;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function startsWithValidDigit($value)
    {
        return strpos($value, '7') === 0 || strpos($value, '8') === 0;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function hasValidLength($value)
    {
        return strlen($value) === 11 && ctype_digit($value);
    }
}