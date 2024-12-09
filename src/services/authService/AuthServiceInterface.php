<?php

namespace vs\yii2\auth\services\authService;

use vs\yii2\auth\services\authService\AuthResourceInterface;
/**
 * Общий интерфейс для сервисов авторизации.
 */
interface AuthServiceInterface
{
    /**
     * @param AuthResourceInterface $authResource
     * @return void
     */
    public function auth(AuthResourceInterface $authResource);
}