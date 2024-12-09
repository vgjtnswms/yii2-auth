<?php

namespace vs\yii2\auth\forms;

use vs\yii2\auth\components\validators\PasswordValidator;
use Yii;
use yii\base\Model;
use yii\base\InvalidConfigException;
use vs\yii2\auth\models\RestoreTokenInterface;
use himiklab\yii2\recaptcha\ReCaptchaValidator;

class PasswordRecoveryForm extends Model implements PasswordRecoveryFormInterface
{
    /**
     * Капча
     *
     * @var string
     */
    public $captcha;

    /**
     * @var string
     */
    public $siteKey;

    /**
     * @var string
     */
    public $secret;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $password_duplicate;

    /**
     * Токен на сброс пароля.
     *
     * @var string
     */
    public $fk;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        // Проверка наличия модуля 'auth'
        if (Yii::$app->getModule('auth') === null) {
            throw new InvalidConfigException('Модуль "auth" не настроен. Пожалуйста, убедитесь, что он указан в конфигурации.');
        }

        // Проверка наличия компонента 'reCaptcha'
        if (Yii::$app->has('reCaptcha') === false) {
            throw new InvalidConfigException('Компонент «reCaptcha» не настроен. Пожалуйста, убедитесь, что он указан в конфигурации.');
        }
        $this->siteKey = Yii::$app->get('reCaptcha')->siteKey;
        $this->secret = Yii::$app->get('reCaptcha')->secret;

        if (Yii::$container->has(RestoreTokenInterface::class) === false) {
            throw new InvalidConfigException('Модель «'.RestoreTokenInterface::class.'» не настроена. Пожалуйста, убедитесь, что она указана в конфигурации.');
        }
    }

    /**
     * @inheritDoc
     */
    public function scenarios()
    {
        return [
            PasswordRecoveryFormInterface::SCENARIO_REQUEST => ['username', 'captcha'],
            PasswordRecoveryFormInterface::SCENARIO_RESET => ['password', 'password_duplicate', 'fk'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['captcha'], ReCaptchaValidator::class, 'message' => Yii::t('auth.main', 'Необходимо заполнить «Капча».'), 'on' => PasswordRecoveryFormInterface::SCENARIO_REQUEST],
            [['username'], 'trim', 'on' => PasswordRecoveryFormInterface::SCENARIO_REQUEST],
            [['username'], 'required', 'on' => PasswordRecoveryFormInterface::SCENARIO_REQUEST],

            [['password', 'password_duplicate', 'fk'], 'required', 'on' => PasswordRecoveryFormInterface::SCENARIO_RESET],
            [['password'], PasswordValidator::class],
            ['password', 'string', 'min' => 6, 'max' => 72, 'on' => PasswordRecoveryFormInterface::SCENARIO_RESET],
            ['password_duplicate', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('auth.main', 'Пароли не совпадают.'), 'on' => PasswordRecoveryFormInterface::SCENARIO_RESET],
            ['fk', 'trim', 'on' => PasswordRecoveryFormInterface::SCENARIO_RESET],
            ['fk', 'required', 'on' => PasswordRecoveryFormInterface::SCENARIO_RESET],
            ['fk', 'string', 'min' => 6, 'max' => 72, 'on' => PasswordRecoveryFormInterface::SCENARIO_RESET],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('auth.main', 'Логин'),
            'password' => Yii::t('auth.main', 'Новый пароль'),
            'password_duplicate' => Yii::t('auth.main', 'Повтор пароля'),
            'fk' => Yii::t('auth.main', 'Токен')
        ];
    }

    /**
     * @return RestoreTokenInterface|null
     */
    public function validateToken()
    {
        $restoreTokenClass = Yii::$container->getDefinitions()[RestoreTokenInterface::class]['class'];

        return $restoreTokenClass::find()
            ->where([
                'token' => $this->fk,
                'used_ts' => null
            ])
            ->andWhere('expiration_ts > now()')
            ->orderBy('ts DESC')
            ->limit(1)
            ->one();
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
    public function getHash()
    {
        return $this->fk;
    }

    /**
     * @inheritDoc
     */
    public function setHash($hash)
    {
        $this->fk = $hash;
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
    public function getPasswordDuplicate()
    {
        return $this->password_duplicate;
    }

    /**
     * @inheritDoc
     */
    public function setPasswordDuplicate($passwordDuplicate)
    {
        $this->password_duplicate = $passwordDuplicate;
    }
}