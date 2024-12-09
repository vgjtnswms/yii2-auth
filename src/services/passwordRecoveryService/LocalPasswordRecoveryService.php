<?php

namespace vs\yii2\auth\services\passwordRecoveryService;

use Yii;
use vs\yii2\auth\Module;
use vs\yii2\auth\models\RestoreTokenInterface;
use vs\yii2\auth\models\AuthCredentialsInterface;
use vs\yii2\auth\forms\PasswordRecoveryFormInterface;
use vs\yii2\auth\services\MailServiceInterface;
use vs\yii2\auth\exceptions\PasswordRecoveryServiceException;
use yii\base\InvalidConfigException;

class LocalPasswordRecoveryService implements PasswordRecoveryServiceInterface
{
    /**
     * @var string Шаблон письма
     */
    public $emailRecoveryLinkTemplate = '@auth/views/recovery-link';

    /**
     * @var bool Отправитель
     */
    public $robotEmail = null;


    public function __construct() {
        /** @var Module */
        $module = Yii::$app->getModule('auth');
        if ($module === null) {
            throw new InvalidConfigException('Модуль "auth" не настроен. Пожалуйста, убедитесь, что он указан в конфигурации.');
        }
        $this->robotEmail = $module->robotEmail;

        if (Yii::$container->has(MailServiceInterface::class) === false) {
            throw new InvalidConfigException('Сервис «'.MailServiceInterface::class.'» не настроен. Пожалуйста, убедитесь, что он указан в конфигурации.');
        }

        if (Yii::$container->has(AuthCredentialsInterface::class) === false) {
            throw new InvalidConfigException('Модель «'.AuthCredentialsInterface::class.'» не настроена. Пожалуйста, убедитесь, что она указана в конфигурации.');
        }

        if (Yii::$container->has(RestoreTokenInterface::class) === false) {
            throw new InvalidConfigException('Модель «'.RestoreTokenInterface::class.'» не настроена. Пожалуйста, убедитесь, что она указана в конфигурации.');
        }
    }

    /**
     * Функция-обертка над методами генерации и отправки.
     *
     * @param PasswordRecoveryResourceInterface $passwordRecoveryResource
     *
     * @return void
     */
    public function generateAndSendRecoveryLink(PasswordRecoveryResourceInterface $passwordRecoveryResource)
    {
        $token = $this->generateToken($passwordRecoveryResource);
        $this->sendRecoveryLink($token, $passwordRecoveryResource);
    }

    /**
     * Генерирует токен для дальнейшего восстановления пароля.
     *
     * @param PasswordRecoveryResourceInterface $resource
     *
     * @return RestoreTokenInterface Сгенерированный токен
     * @throws PasswordRecoveryServiceException
     */
    public function generateToken(PasswordRecoveryResourceInterface $resource): RestoreTokenInterface
    {
        // Нет реализации
    }

    /**
     * Отправляет письмо со ссылкой для восстановления пароля.
     *
     * @param RestoreTokenInterface $token
     * @param PasswordRecoveryResourceInterface $resource
     *
     * @return void
     * @throws PasswordRecoveryServiceException
     */
    protected function sendRecoveryLink(RestoreTokenInterface $token, PasswordRecoveryResourceInterface $resource)
    {
        // Нет реализации
    }

    /**
     * Отправляет запроc на смену пароля.
     *
     * @param PasswordRecoveryFormInterface $form
     *
     * @return mixed Пользователь или true.
     * @throws PasswordRecoveryServiceException
     */
    public function changePassword(PasswordRecoveryFormInterface $form)
    {
        // Нет реализации
    }
}
