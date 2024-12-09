<?php

namespace vs\yii2\auth\forms;

use vs\yii2\auth\services\authService\AuthResourceInterface;

/**
 * Interface LoginFormInterface.
 * 
 * Интерфейс для реализации логики формы входа.
 */
interface LoginFormInterface extends FormInterface, AuthResourceInterface
{
    /**
     * Увеличивает количество неудачных попыток аутентификации.
     *
     * @return void
     */
    public function incrementAuthAttempts();

    /**
     * Сбрасывает количество неудачных попыток аутентификации.
     *
     * @return void
     */
    public function resetAuthAttempts();

    /**
     * Возвращает продолжительность действия опции "Запомнить меня".
     *
     * @return int Продолжительность в секундах, на которую пользователь будет запомнен.
     */
    public function getRememberMeDuration();

    /**
     * Возвращает значение опции "Запомнить меня".
     *
     * @return bool
     */
    public function getRememberMe();

    /**
     * Устанавливает значение опции "Запомнить меня".
     *
     * @param bool $rememberMe
     * @return void
     */
    public function setRememberMe($rememberMe);
}