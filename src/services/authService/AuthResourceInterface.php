<?php

namespace vs\yii2\auth\services\authService;

/**
 * Интерфейс для ресурса авторизации.
 *
 * @package
 */
interface AuthResourceInterface
{
    /**
     * Возвращает имя пользователя.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Устанавливает имя пользователя.
     *
     * @param string $username
     * @return void
     */
    public function setUsername($username);

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
}