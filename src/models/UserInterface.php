<?php

namespace vs\yii2\auth\models;

use yii\web\IdentityInterface;

/**
 * Интерфейс UserInterface
 *
 * Этот интерфейс определяет методы, которые должны быть реализованы моделью пользователя
 * в основном проекте. Он гарантирует, что модель пользователя имеет необходимые методы
 * для целей аутентификации.
 */
interface UserInterface extends IdentityInterface
{
    /**
     * Undocumented function
     *
     * @return bool
     */
    public function isDeleted();
}