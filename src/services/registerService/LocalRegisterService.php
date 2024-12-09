<?php

namespace vs\yii2\auth\services\registerService;

use Yii;
use vs\yii2\auth\forms\RegistrationFormInterface;
use vs\yii2\auth\models\UserInterface;
use vs\yii2\auth\exceptions\RegisterServiceException;

/**
 * Сервис регистрации пользователя.
 */
class LocalRegisterService implements RegisterServiceInterface
{
    /**
     * Регистрация пользователя в локальной базе данных.
     *
     * @param RegisterResourceInterface $registerResource Данные для регистрации.
     *
     * @return UserInterface
     * @throws RegisterServiceException Если метод регистрации не поддерживается.
     */
    public function register(RegisterResourceInterface $registerResource)
    {
        if ($registerResource instanceof RegistrationFormInterface) {
            return $this->registerByForm($registerResource);
        }

        throw new RegisterServiceException(Yii::t('auth.main', 'Неподдерживаемый метод регистрации.'));
    }

    /**
     * Регистрация пользователя по стандартной форме.
     *
     * @param RegistrationFormInterface $form Форма регистрации.
     *
     * @return UserInterface
     * @throws RegisterServiceException Если форма не прошла валидацию или не удалось создать учетную запись.
     */
    protected function registerByForm(RegistrationFormInterface $form)
    {
        if (!$form->validate()) {
            throw new RegisterServiceException(Yii::t('auth.main', 'Форма не прошла валидацию.'));
        }

        // Нет реализации
    }

    /**
     * Создание профиля пользователя.
     *
     * @param RegistrationFormInterface $form Форма регистрации.
     * @param array $response Данные запроса.
     *
     * @return UserInterface
     * @throws RegisterServiceException Если не удалось создать запись для пользователя.
     */
    protected function createProfile($form, $response): UserInterface
    {
        // Нет реализации
    }
}