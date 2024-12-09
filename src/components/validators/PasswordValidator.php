<?php

namespace vs\yii2\auth\components\validators;

use stdClass;
use Yii;
use yii\validators\Validator;

/**
 * Валидация пароля
 */
class PasswordValidator extends Validator
{
    /**
     * @var stdClass
     */
    protected $num;

    /**
     * @var stdClass
     */
    protected $bonus;

    /**
     * @var int
     */
    protected $score = 0;

    /**
     * @var int
     */
    protected $baseScore = 25;

    /**
     * @var int
     */
    protected $minPasswordLength = 8;

    /**
     * @var boolean
     */
    protected $is_exception = false;

    /**
     * @var array
     */
    protected $exceptions = ['o', 'I', 'i'];

    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->setNum();
        $this->setBonus();
    }

    /**
     * @return void
     */
    public function setNum(): void
    {
        $num = new stdClass();
        $num->upper = 0;
        $num->lower = 0;
        $num->excess = 0;
        $num->numbers = 0;
        $num->symbols = 0;
        $this->num = $num;
    }

    /**
     * @return void
     */
    public function setBonus(): void
    {
        $bonus = new stdClass();
        $bonus->upper = 1;
        $bonus->lower = 1;
        $bonus->excess = 0;
        $bonus->numbers = 1;
        $bonus->symbols = 2;
        $bonus->combo = 0;
        $this->bonus = $bonus;
    }

    /**
     * @param mixed $model
     * @param mixed $attribute
     * @return bool
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (!empty($value)) {
            if (preg_match('/[^\\p{Common}\\p{Latin}]/u',$value) == 1) {
                $this->addError($model, $attribute, Yii::t("main","Должен содержать только латинские буквы"));
                return false;
            }

            if (strlen($value) < 8) {
                $this->baseScore = 0;
                $this->addError($model, $attribute, Yii::t("main", "Пароль должен быть не менее 8 символов"));
                return false;
            }

            $this->analyzeString($value);
            $this->calcComplexity();

            if ($this->is_exception) {
                $this->addError($model, $attribute, Yii::t("main", "В пароле во избежание путаницы исключить символы 0(ноль), о(буква), l(буква), i(буква)"));
                return false;
            }

            if ($this->score < 50) {
                $this->addError($model, $attribute, Yii::t("main", "Пароль должен содержать прописную букву английского алфавита от A до Z, десятичную цифру (от 1 до 9), неалфавитный символ (например: !, $, #, %)"));
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $str
     * @return void
     */
    protected function analyzeString(string $str)
    {
        for ($i = 0; $i < strlen($str); $i++) {
            if (preg_match ('/[A-Z]/', $str[$i])) $this->num->upper++;
            if (preg_match ('/[a-z]/', $str[$i])) $this->num->lower++;
            if (preg_match ('/[0-9]/', $str[$i])) $this->num->numbers++;
            if (preg_match ('/[!@#$%^&*?_~]/', $str[$i])) $this->num->symbols++;

            if (in_array($str[$i], $this->exceptions) || preg_match ('/[0]/', $str[$i])) {
                $this->is_exception = true;
            }
        }

        if (
            $this->num->upper == 0
            || $this->num->lower == 0
            || $this->num->numbers == 0
            || $this->num->symbols == 0
        ) {
            $this->baseScore -= 25;
        }

        $this->num->excess = strlen($str) - $this->minPasswordLength;

        if ($this->num->upper && $this->num->numbers && $this->num->symbols && $this->num->lower) {
            $this->bonus->combo = 25;
        } else if (
            ($this->num->upper && $this->num->numbers) || ($this->num->upper && $this->num->symbols)
            || ($this->num->numbers && $this->num->symbols)
        ) {
            $this->bonus->combo = 10;
        }

        if (preg_match ('/^[\\sa-z]+$/', $str)) {
            $this->baseScore -= 15;
        }

        if (preg_match ('/^[\\s0-9]+$/', $str)) {
            $this->baseScore -= 15;
        }
    }

    /**
     * @return void
     */
    protected function calcComplexity()
    {
        $this->score = $this->baseScore + $this->bonus->combo + ($this->num->lower*$this->bonus->lower)
            + ($this->num->upper*$this->bonus->upper) + ($this->num->numbers*$this->bonus->numbers)
            + ($this->num->excess*$this->bonus->excess) + ($this->num->symbols*$this->bonus->symbols);
    }
}
