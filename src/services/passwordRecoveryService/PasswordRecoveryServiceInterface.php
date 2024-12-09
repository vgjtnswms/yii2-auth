<?php

namespace vs\yii2\auth\services\passwordRecoveryService;

use vs\yii2\auth\forms\PasswordRecoveryFormInterface;
use vs\yii2\auth\models\RestoreTokenInterface;
use vs\yii2\auth\exceptions\PasswordRecoveryServiceException;

/**
 * Общий интерфейс для сервисов восстановления пароля.
 */
interface PasswordRecoveryServiceInterface
{
    /**
     * Функция-обертка над методами генерации и отправки.
     *
     * @param PasswordRecoveryResourceInterface $passwordRecoveryResource
     *
     * @return mixed
     * @throws PasswordRecoveryServiceException
     */
    public function generateAndSendRecoveryLink(PasswordRecoveryResourceInterface $passwordRecoveryResource);

    /**
     * Генерирует токен для дальнейшего восстановления пароля.
     *
     * @param PasswordRecoveryResourceInterface $passwordRecoveryResource
     *
     * @return RestoreTokenInterface
     * @throws PasswordRecoveryServiceException
     */
    public function generateToken(PasswordRecoveryResourceInterface $passwordRecoveryResource): RestoreTokenInterface;

    /**
     * Сбрасывает пароль.
     *
     * @param PasswordRecoveryFormInterface $form
     *
     * @return mixed
     * @throws PasswordRecoveryServiceException
     */
    public function changePassword(PasswordRecoveryFormInterface $form);

}