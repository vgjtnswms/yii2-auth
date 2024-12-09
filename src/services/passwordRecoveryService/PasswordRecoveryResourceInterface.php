<?php

namespace vs\yii2\auth\services\passwordRecoveryService;

interface PasswordRecoveryResourceInterface
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
}