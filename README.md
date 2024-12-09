# Модуль auth для Yii2

Модуль предоставляет функциональность регистрации, авторизации и восстановления пароля для Yii2.

## Технологии

- [Yii2](https://www.yiiframework.com/)

## Ссылки

- `{host}/auth/auth/registration`: Регистрация.
- `{host}/auth/auth/send-verification-code`: Получение кода для подтверждения почты.
- `{host}/auth/auth/login`: Авторизация.
- `{host}/auth/auth/logout`: Выход.
- `{host}/auth/auth/restore`: Запрос на восстановление пароля.
- `{host}/auth/auth/reset-password?fk={reset-token}`: Сброс пароля.

## Использование

### Настройка модуля в `web.php`:

Параметры модуля:
- rememberMeDuration: Время жизни сессии "Запомнить меня" в секундах (по умолчанию 1 месяц).
- authAttemptsBeforeCaptcha: Количество попыток перед показом капчи (по умолчанию 3).
- userAgreementLink: Ссылка на пользовательское соглашение.
- privacyPolicyLink: Ссылка на политику конфиденциальности.
- robotEmail: Почта, от которой отправляются письма.

Компоненты, которые используются в модуле:
- user: Компонент пользователя.
- registerService: Сервис регистрации.
- authService: Сервис аутентификации.
- passwordRecoveryService: Сервис восстановления пароля.
- reCaptcha: Настройка Google reCAPTCHA.
- i18n: Локализация.
- urlManager: Настройка ссылок.

```php
$config = [
    'modules' => [
        'auth' => [
            'class' => 'vs\yii2\auth\Module',
            'rememberMeDuration' => 3600 * 24 * 30, // Время жизни сессии "Запомнить меня"
            'authAttemptsBeforeCaptcha' => 100, // Количество попыток перед показом капчи
            'userAgreementLink' => '/user-agreement', // Ссылка на пользовательское соглашение
            'privacyPolicyLink' => '/privacy-policy', // Ссылка на политику конфиденциальности
            'robotEmail' => 'robot@email.ru', // Почта, от которой отправляются письма.
        ],
        // Другие модули
    ],
    'components' => [
        'user' => [
            'class' => 'vs\yii2\auth\components\User',
            'identityClass' => 'vs\yii2\auth\models\User',
            'loginUrl' => '/auth/auth/login',
            'enableAutoLogin' => true,
        ],
        'registerService' => [
            'class' => \vs\yii2\auth\services\registerService\LocalRegisterService::class,
        ],
        'authService' => [
            'class' => \vs\yii2\auth\services\authService\LocalAuthService::class,
        ],
        'passwordRecoveryService' => [
            'class' => \vs\yii2\auth\services\passwordRecoveryService\LocalPasswordRecoveryService::class,
        ],
        'reCaptcha' => [
            'name' => 'captcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => '',
            'secret' => '',
        ],
        'i18n' => [
            'translations' => [
                'auth*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/vs/yii2-auth/src/messages',
                    'sourceLanguage' => 'ru-RU',
                ],
                // Другие переводы
            ],
        ],
        'urlManager' => [
            'rules' => [
                'auth/login' => 'auth/auth/login',
                'auth/logout' => 'auth/auth/logout',
                'auth/registration' => 'auth/auth/registration',
                'auth/send-verification-code' => '/auth/auth/send-verification-code',
                'auth/restore' => 'auth/auth/restore',
                'auth/reset-password' => 'auth/auth/reset-password',
                // Другие правила
            ],
            // Другие настройки
        ],
        // Другие компоненты
    ],
    // Другие настройки
];
```

### Настройка контейнера в `container.php`:

```php
return $container = [
    'definitions' => [
        \vs\yii2\auth\forms\LoginFormInterface::class => \vs\yii2\auth\forms\LoginForm::class,
        \vs\yii2\auth\forms\PasswordRecoveryFormInterface::class => \vs\yii2\auth\forms\PasswordRecoveryForm::class,
        \vs\yii2\auth\forms\RegistrationFormInterface::class => \vs\yii2\auth\forms\RegistrationForm::class,
        \vs\yii2\auth\models\UserInterface::class => \vs\yii2\auth\models\User::class,
        \vs\yii2\auth\models\AuthCredentialsInterface::class => \vs\yii2\auth\models\AuthCredentials::class,
        \vs\yii2\auth\models\RestoreTokenInterface::class => \vs\yii2\auth\models\RestoreToken::class,
        \vs\yii2\auth\services\MailServiceInterface::class => \vs\yii2\auth\services\MailService::class,
        ...
    ],
    ...
];
```

```php
$container = require __DIR__ . '/protected/config/container.php';
$config['container'] = $container;
(new yii\web\Application($config))->run();
```

### Применение миграций в `console.php`:

```php
$config = [
    // Другие настройки
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@app/migrations',
                // Другие пути к миграциям
                '@vendor/vs/yii2-auth/src/migrations',
            ],
        ],
    ],
];
```

```bash
php yii migrate
```

### Настройка composer в `composer.json`:

```json
{
  ...
  "require": {
    ...
    "vs/yii2-auth": "0.7.1"
  },
  "repositories": [
    ...
    {
      "type": "path",
      "url": "./libraries/vs/yii2-auth"
    }
  ]
}
```

или

```json
{
  ...
  "require": {
    ...
    "vs/yii2-auth": "0.7.1"
  },
  "config": {
    ...
    "secure-http":false
  },
  "repositories": [
    ...
    {
      "type": "git",
      "branch": "master",
      "url": "http://git.vpn/backend/auth.git"
    }
  ]
}
```

```bash
composer require vs/yii2-auth
```