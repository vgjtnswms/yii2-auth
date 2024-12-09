<?php

namespace vs\yii2\auth;

use Yii;

/**
 * Модуль auth для Yii2.
 * Регистрация, авторизация, восстановление пароля.
 *
 * Ссылки:
 * - {host}/auth/auth/registration
 * - {host}/auth/auth/send-verification-code
 * - {host}/auth/auth/login
 * - {host}/auth/auth/logout
 * - {host}/auth/auth/restore
 * - {host}/auth/auth/reset-password?fk={reset-token}
 *
 * Пример конфигурации.
 *
 * Настройка модуля в web.php:
 * $config = [
 *     'modules' => [
 *         ...
 *         'auth' => [
 *             'class' => 'vs\yii2\auth\Module',
 *             'controllerMap' => [
 *                 'auth' => 'vs\yii2\auth\controllers\AuthController',
 *             ],
 *             'rememberMeDuration' => 3600 * 24 * 30,
 *             'authAttemptsBeforeCaptcha' => 3,
 *             'userAgreementLink' => '/user-agreement',
 *             'privacyPolicyLink' => '/privacy-policy',
 *         ],
 *         ...
 *     ],
 *     'components' => [
 *         'user' => [
 *             'class' => 'vs\yii2\auth\components\User',
 *             'identityClass' => 'vs\yii2\auth\models\User',
 *             'loginUrl' => '/auth/auth/login',
 *             'enableAutoLogin' => true,
 *         ],
 *         'registerService' => [
 *             'class' => \vs\yii2\auth\services\registerService\LocalRegisterService::class
 *         ],
 *         'authService' => [
 *             'class' => \vs\yii2\auth\services\authService\LocalAuthService::class
 *         ],
 *         'passwordRecoveryService' => [
 *             'class' => \vs\yii2\auth\services\passwordRecoveryService\LocalPasswordRecoveryService::class
 *         ],
 *         'reCaptcha' => [
 *             'name' => 'captcha',
 *             'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
 *             'siteKey' => '',
 *             'secret' => '',
 *         ],
 *         'i18n' => [
 *             'translations' => [
 *                 ...
 *                 'auth*' => [
 *                     'class' => 'yii\i18n\PhpMessageSource',
 *                     'basePath' => '@vendor/vs/yii2-auth/src/messages',
 *                     'sourceLanguage' => 'ru-RU',
 *                 ],
 *                 ...
 *             ],
 *         ],
 *         'urlManager' => [
 *             ...
 *             'rules' => [
 *                 'auth/login' => 'auth/auth/login',
 *                 'auth/logout' => 'auth/auth/logout',
 *                 'auth/registration' => 'auth/auth/registration',
 *                 'auth/send-verification-code' => '/auth/auth/send-verification-code',
 *                 'auth/restore' => 'auth/auth/restore',
 *                 'auth/reset-password' => 'auth/auth/reset-password',
 *                 ...
 *             ],
 *             ...
 *         ],
 *         ...
 *     ]
 *     ...
 * ];
 *
 * Настройка контенера в container.php:
 * $container = [
 *     'definitions' => [
 *         \vs\yii2\auth\forms\LoginFormInterface::class => \vs\yii2\auth\forms\LoginForm::class,
 *         \vs\yii2\auth\forms\PasswordRecoveryFormInterface::class => \vs\yii2\auth\forms\PasswordRecoveryForm::class,
 *         \vs\yii2\auth\forms\RegistrationFormInterface::class => \vs\yii2\auth\forms\RegistrationForm::class,
 *         \vs\yii2\auth\models\UserInterface::class => \vs\yii2\auth\models\User::class,
 *         \vs\yii2\auth\models\AuthCredentialsInterface::class => \vs\yii2\auth\models\AuthCredentials::class,
 *         \vs\yii2\auth\models\RestoreTokenInterface::class => \vs\yii2\auth\models\RestoreToken::class,
 *         \vs\yii2\auth\services\MailServiceInterface::class => \vs\yii2\auth\services\MailService::class,
 *         ...
 *     ],
 *     ...
 * ];
 *
 * Применение миграций в console.php:
 * $config = [
 *     ...
 *     'controllerMap' => [
 *         'migrate' => [
 *             'class' => 'yii\console\controllers\MigrateController',
 *                 'migrationPath' => [
 *                     '@app/migrations',
 *                     ...
 *                     '@vendor/vs/yii2-auth/src/migrations'
 *                     ...
 *                 ],
 *             ],
 *         ],
 *     ]
 *     ...
 * ];
 *
 */
class Module extends \yii\base\Module
{
    /**
     * Пространство имен контроллеров.
     *
     * @var string
     */
    public $controllerNamespace = 'vs\yii2\auth\controllers';

    /**
     * Ссылка на пользовательское соглашение.
     *
     * @var string|null
     */
    public $userAgreementLink = null;

    /**
     * Ссылка на политику конфиденциальности.
     *
     * @var string|null
     */
    public $privacyPolicyLink = null;

    /**
     * Время жизни сессии "Запомнить меня" (1 месяц по умолчанию).
     *
     * @var int 
     */
    public $rememberMeDuration = 3600 * 24 * 30;

    /**
     * Количество попыток перед показом капчи (3 по умолчанию).
     *
     * @var int 
     */
    public $authAttemptsBeforeCaptcha = 3;

    /**
     * Время жизни токена для сброса пароля (1 час по умолчанию).
     *
     * @var int
     */
    public $passwordResetTokenExpire = 3600;

    /**
     * Почта для отправки писем роботом.
     *
     * @var string
     */
    public $robotEmail = '...';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->getI18n()->translations['auth.*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => __DIR__.'/messages',
        ];

        $this->setAliases([
            '@auth' => '@vendor/vs/yii2-auth/src'
        ]);
    }
}