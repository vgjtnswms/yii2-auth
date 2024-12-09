<?php

namespace vs\yii2\auth\forms;

use Yii;
use yii\base\Model;
use yii\base\InvalidConfigException;
use himiklab\yii2\recaptcha\ReCaptchaValidator;

/**
 * Класс LoginForm реализует логику формы входа.
 */
class LoginForm extends Model implements LoginFormInterface
{
    /**
     * Ключ для хранения количества попыток входа в сессию
     */
    const AUTH_ATTEMPTS_SESSION_KEY = 'AUTH_ATTEMPTS_SESSION';

    /**
     * Количество попыток перед показом капчи
     */
    const AUTH_ATTEMPTS_BEFORE_CAPTCHA = 3;

    /**
     * Время жизни сессии пользователя (1 месяц)
     */
    const REMEMBER_ME_DURATION = 3600 * 24 * 30;

    /**
     * @var string Имя пользователя
     */
    public $username;

    /**
     * @var string Пароль пользователя
     */
    public $password;

    /**
     * @var bool Запомнить пользователя
     */
    public $rememberMe = true;

    /**
     * @var string Капча
     */
    public $captcha;

    /**
     * @var int Количество попыток перед показом капчи
     */
    public $authAttemptsBeforeCaptcha;

    /**
     * @var int Время жизни сессии "Запомнить меня"
     */
    public $rememberMeDuration;

    /**
     * @var bool Использовать капчу
     */
    public $useCaptcha = false;

    /**
     * @var string
     */
    public $siteKey;

    /**
     * @var string
     */
    public $secret;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        /** @var \vs\yii2\auth\Module */
        $module = Yii::$app->getModule('auth');
        if ($module === null) {
            throw new InvalidConfigException('Модуль "auth" не настроен. Пожалуйста, убедитесь, что он указан в конфигурации.');
        }
        $this->rememberMeDuration = $module->rememberMeDuration ?: self::REMEMBER_ME_DURATION;
        $this->authAttemptsBeforeCaptcha = $module->authAttemptsBeforeCaptcha ?: self::AUTH_ATTEMPTS_BEFORE_CAPTCHA;

        // Проверка наличия компонента "reCaptcha"
        if (Yii::$app->has('reCaptcha') === false) {
            throw new InvalidConfigException('Компонент «reCaptcha» не настроен. Пожалуйста, убедитесь, что он указан в конфигурации.');
        }
        $reCaptcha = Yii::$app->get('reCaptcha');
        $this->siteKey = $reCaptcha->siteKey;
        $this->secret = $reCaptcha->secret;

        // Проверка наличия компонента "session"
        if (!Yii::$app->has('session')) {
            throw new InvalidConfigException('Компонент «session» не настроен.');
        }
        if (Yii::$app->session->get(self::AUTH_ATTEMPTS_SESSION_KEY, 0) >= $this->authAttemptsBeforeCaptcha) {
            $this->useCaptcha = true;
        }
    }

    /**
     * Правила валидации для атрибутов модели
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password'], 'filter', 'filter' => 'trim'],
            [['username'], 'string', 'min' => 5, 'max' => 50],
            [['password'], 'string', 'min' => 5, 'max' => 50],
            [['rememberMe'], 'filter', 'filter' => function ($value) {
                return (bool)$value;
            }],
            [['rememberMe'], 'boolean'],
            [['captcha'], ReCaptchaValidator::class, 'when' => function ($model) {return $this->useCaptcha;}, 'message' => Yii::t('auth.main', 'Необходимо заполнить «{attribute}».')]
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('auth.main', 'Логин'),
            'password' => Yii::t('auth.main', 'Пароль'),
            'rememberMe' => Yii::t('auth.main', 'Запомнить меня'),
            'captcha' => Yii::t('auth.main', 'Капча'),
        ];
    }

    /**
     * Увеличивает количество неудачных попыток аутентификации.
     *
     * Метод получает текущее количество попыток из сессии, увеличивает его на 1 и
     * сохраняет обратно в сессию.
     *
     * @return void
     */
    public function incrementAuthAttempts()
    {
        $i = Yii::$app->session->get(self::AUTH_ATTEMPTS_SESSION_KEY, 0) + 1;
        Yii::$app->session->set(self::AUTH_ATTEMPTS_SESSION_KEY, $i);
    }

    /**
     * Сбрасывает количество неудачных попыток аутентификации.
     *
     * Метод удаляет ключ из сессии, который хранит количество неудачных попыток входа.
     *
     * @return void
     */
    public function resetAuthAttempts()
    {
        Yii::$app->session->remove(self::AUTH_ATTEMPTS_SESSION_KEY);
    }

    /**
     * @return int
     */
    public function getRememberMeDuration()
    {
        return $this->rememberMe ? (int)$this->rememberMeDuration : 0;
    }

    /**
     * @return string
     */
    public function getSiteKey()
    {
        return $this->siteKey;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @inheritDoc
     */
    public function getRememberMe()
    {
        return $this->rememberMe;
    }

    /**
     * @inheritDoc
     */
    public function setRememberMe($rememberMe)
    {
        $this->rememberMe = $rememberMe;
    }
}