<?php

namespace vs\yii2\auth\forms;

use vs\yii2\auth\services\passwordRecoveryService\PasswordRecoveryResourceInterface;

interface PasswordRecoveryFormInterface extends FormInterface, PasswordRecoveryResourceInterface
{
    /**
     * Запрос на сброс пароля
     *
     * @var string
     */
    const SCENARIO_REQUEST = 'request';

    /**
     * Сброс пароля
     *
     * @var string
     */
    const SCENARIO_RESET = 'reset';

    /**
     * @return void
     */
    public function validateToken();

    /**
     * Возвращает хеш.
     *
     * @return string
     */
    public function getHash();

    /**
     * Устанавливает хеш.
     *
     * @param string $hash
     * @return void
     */
    public function setHash($hash);

    /**
     * Возвращает пароль.
     *
     * @return string
     */
    public function getPassword();

    /**
     * Устанавливает пароль.
     *
     * @param string $password
     * @return void
     */
    public function setPassword($password);

    /**
     * Возвращает дублирующий пароль.
     *
     * @return string
     */
    public function getPasswordDuplicate();

    /**
     * Устанавливает дублирующий пароль.
     *
     * @param string $passwordDuplicate
     * @return void
     */
    public function setPasswordDuplicate($passwordDuplicate);
}