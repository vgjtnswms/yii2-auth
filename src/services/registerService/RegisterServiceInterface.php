<?php

namespace vs\yii2\auth\services\registerService;

/**
 * Общий интерфейс для сервисов регистрации.
 * 
 * Интерфейс определяет метод для регистрации новых пользователей
 * в системе.
 */
interface RegisterServiceInterface
{
    /**
     * Регистрирует новый ресурс.
     *
     * Этот метод принимает объект `RegisterResourceInterface`, который содержит
     * информацию, необходимую для регистрации нового пользователя в системе.
     *
     * @param RegisterResourceInterface $registerResource Объект, содержащий данные для регистрации.
     * @return mixed Результат операции регистрации, который может включать
     *               успешный результат или сообщение об ошибке.
     */
    public function register(RegisterResourceInterface $registerResource);
}