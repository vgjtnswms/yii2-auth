<?php

namespace vs\yii2\auth\models;

/**
 * Undocumented interface
 */
interface AuthCredentialsInterface extends ModelInterface
{
    const TYPE_TOKEN = 'token';
    const TYPE_LOGIN = 'login';
    const TYPE_PHONE = 'phone';
    const TYPE_EMAIL = 'email';

    const SCENARIO_CHANGE_PASSWORD = 'change_password';

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param string $validation Пароль.
     * @return void
     */
    public function setValidation($validation);
}
