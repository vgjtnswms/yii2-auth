<?php

namespace vs\yii2\auth\forms;

use vs\yii2\auth\services\registerService\RegisterResourceInterface;
use vs\yii2\auth\exceptions\RegisterServiceException;

interface RegistrationFormInterface extends FormInterface, RegisterResourceInterface
{
    /**
     * Проверка кода подтверждения электронной почты.
     *
     * @param string $attribute Имя атрибута, который проверяется.
     * @param array $params Дополнительные параметры.
     */
    public function validateEmailVerificationCode($attribute, $params);

    /**
     * Отправка кода подтверждения на почту.
     *
     * @throws RegisterServiceException Если код не отправлен.
     * @return bool True если код успешно отправлен.
     */
    public function sendEmailVerificationCode();
}