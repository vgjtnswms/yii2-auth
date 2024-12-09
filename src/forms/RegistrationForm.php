<?php

namespace vs\yii2\auth\forms;

use Yii;
use yii\base\Model;
use vs\yii2\auth\Module;
use vs\yii2\auth\services\MailServiceInterface;
use vs\yii2\auth\components\validators\PhoneNumberValidator;
use vs\yii2\auth\components\validators\PasswordValidator;
use himiklab\yii2\recaptcha\ReCaptchaValidator;
use vs\yii2\auth\exceptions\RegisterServiceException;
use vs\yii2\auth\exceptions\MailServiceException;
use yii\base\InvalidConfigException;

class RegistrationForm extends Model implements RegistrationFormInterface
{
    /**
     * Ключ для хранения кода подтверждения электронной почты в кэше.
     */
    const EMAIL_VERIFICATION_CODE_CACHE_KEY = 'email_verification_code_{email}';

    /**
     * Ключ для хранения времени истечения кода подтверждения электронной почты в кэше.
     */
    const EMAIL_VERIFICATION_CODE_EXPIRE_CACHE_KEY = 'email_verification_code_expire_{email}';

    /**
     * Ключ для хранения времени последней отправки кода подтверждения в кэше.
     */
    const LAST_SEND_TIME_CACHE_KEY = 'last_send_time_{email}';

    /**
     * @var string Шаблон письма
     */
    public $emailVerificationCodeTemplate = '@auth/views/email-verification-code';

    /**
     * @var string Отправитель
     */
    public $robotEmail = null;

    /**
     * @var string Фамилия
     */
    public $lastName;

    /**
     * @var string Имя
     */
    public $firstName;

    /**
     * @var string|null Отчество (при наличии)
     */
    public $middleName;

    /**
     * @var string Адрес электронной почты
     */
    public $email;

    /**
     * @var string Код подтверждения электронной почты
     */
    public $emailVerificationCode;

    /**
     * @var string Номер мобильного телефона
     */
    public $phone;

    /**
     * @var bool Галочка "Я подтверждаю свое согласие со всеми вышеперечисленными пунктами"
     */
    public $agreeTerms;

    /**
     * @var bool Ссылка на пользовательское соглашение
     */
    public $userAgreementLink = null;

    /**
     * @var bool Галочка "С пользовательским соглашением ознакомлен(-а) и согласен(-на)"
     */
    public $agreeUserAgreement;

    /**
     * @var bool Ссылка на политику конфиденциальности
     */
    public $privacyPolicyLink = null;

    /**
     * @var bool Галочка "С политикой конфиденциальности ознакомлен(-а) и согласен(-на)"
     */
    public $agreePrivacyPolicy;

    /**
     * @var string Пароль
     */
    public $password;

    /**
     * @var string Пароль (повтор)
     */
    public $passwordRepeat;

    /**
     * @var bool Использовать капчу
     */
    public $useCaptcha = true;

    /**
     * @var string Капча
     */
    public $captcha;

    /**
     * @var string Ключ сайта для ReCaptcha
     */
    public $siteKey;

    /**
     * @var string Секретный ключ для ReCaptcha
     */
    public $secret;

    /**
     * @throws InvalidConfigException
     * @return void
     */
    public function init()
    {
        parent::init();

        /** @var Module */
        $module = Yii::$app->getModule('auth');
        if ($module === null) {
            throw new InvalidConfigException('Модуль "auth" не настроен. Пожалуйста, убедитесь, что он указан в конфигурации.');
        }
        $this->userAgreementLink = $module->userAgreementLink ?: null;
        $this->privacyPolicyLink = $module->privacyPolicyLink ?: null;
        $this->robotEmail = $module->robotEmail;

        if (Yii::$app->has('reCaptcha') === false) {
            throw new InvalidConfigException('Компонент «reCaptcha» не настроен. Пожалуйста, убедитесь, что он указан в конфигурации.');
        }
        $reCaptcha = Yii::$app->get('reCaptcha');
        $this->siteKey = $reCaptcha->siteKey;
        $this->secret = $reCaptcha->secret;

        if (Yii::$container->has(MailServiceInterface::class) === false) {
            throw new InvalidConfigException('Сервис «'.MailServiceInterface::class.'» не настроен. Пожалуйста, убедитесь, что он указан в конфигурации.');
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lastName', 'firstName', 'email', 'emailVerificationCode', 'phone', 'password', 'passwordRepeat'], 'required'],
            ['middleName', 'string'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => Yii::$app->user->identityClass, 'message' => Yii::t('auth.main', 'Этот адрес электронной почты уже зарегистрирован.')],
            [['phone'], PhoneNumberValidator::class],
            [['agreeTerms', 'agreeUserAgreement', 'agreePrivacyPolicy'], 'filter', 'filter' => 'boolval'],
            ['agreeTerms', 'compare', 'compareValue' => true, 'message' => Yii::t('auth.main', 'Вы должны подтвердить свое согласие.')],
            ['agreeUserAgreement', 'compare', 'compareValue' => true, 'message' => Yii::t('auth.main', 'Вы должны согласиться с пользовательским соглашением.')],
            ['agreePrivacyPolicy', 'compare', 'compareValue' => true, 'message' => Yii::t('auth.main', 'Вы должны согласиться с политикой конфиденциальности.')],
            ['password', 'string', 'min' => 6],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('auth.main', 'Пароли не совпадают.')],
            [['password'], PasswordValidator::class],
            [['captcha'], ReCaptchaValidator::class, 'when' => function () {return $this->useCaptcha;}, 'message' => Yii::t('auth.main', 'Необходимо заполнить «{attribute}».')],
            ['emailVerificationCode', 'validateEmailVerificationCode'],
            ['emailVerificationCode', 'match', 'pattern' => '/^\d{3}-\d{3}$/', 'message' => Yii::t('auth.main', 'Код подтверждения электронной почты должен быть в формате "XXX-XXX".')],
        ];
    }

    /**
     * Проверка кода подтверждения электронной почты.
     *
     * @param string $attribute Имя атрибута, который проверяется.
     * @param array $params Дополнительные параметры.
     */
    public function validateEmailVerificationCode($attribute, $params)
    {
        $cache = Yii::$app->cache;
        $code = $cache->get(str_replace('{email}', $this->email, self::EMAIL_VERIFICATION_CODE_CACHE_KEY));
        $expire = $cache->get(str_replace('{email}', $this->email, self::EMAIL_VERIFICATION_CODE_EXPIRE_CACHE_KEY));

        if (time() > $expire) {
            $this->addError($attribute, Yii::t('auth.main', 'Код подтверждения электронной почты истек. Пожалуйста, получите новый код.'));
        } elseif ($this->$attribute !== $code) {
            $this->addError($attribute, Yii::t('auth.main', 'Код подтверждения электронной почты неверен.'));
        }
    }

    /**
     * Отправка кода подтверждения на почту. Можно сделать 1 раз в три минуты.
     *
     * @throws RegisterServiceException Если код уже был отправлен менее чем 3 минуты назад или не отработал mailer.
     * @return bool True если код успешно отправлен
     */
    public function sendEmailVerificationCode()
    {
        if (!$this->validate(['email'])) {
            throw new RegisterServiceException(Yii::t('auth.main', 'Некорректный адрес электронной почты.'));
        }

        $cache = Yii::$app->cache;
        $lastSendTime = $cache->get(str_replace('{email}', $this->email, self::LAST_SEND_TIME_CACHE_KEY));

        if (time() - $lastSendTime < 180) {
            $email = $this->email;
            $time = date('i:s', 180 - (time() - $lastSendTime));
            throw new RegisterServiceException(Yii::t('auth.main', "Код уже отправлен на указанный адрес электронной почты: {$email}. Получить новый код можно будет через три минуты."));
        }

        $code = $this->generateVerificationCode();

        try {
            /** @var MailServiceInterface $mailService */
            $mailService = Yii::$container->get(MailServiceInterface::class);
            $mailService->sendEmail(
                Yii::t('auth.main', 'Код подтверждения электронной почты'),
                $this->robotEmail,
                $this->email,
                $this->emailVerificationCodeTemplate,
                ['text' => Yii::t('auth.main', 'Ваш код подтверждения:') . ' ' . $code]
            );
        } catch (MailServiceException $e) {
            throw new RegisterServiceException(Yii::t('auth.main', 'Сервис временно недоступен, попробуйте позже.'));
        }

        $cache->set(str_replace('{email}', $this->email, self::LAST_SEND_TIME_CACHE_KEY), time(), 180); // 3 минуты
        $cache->set(str_replace('{email}', $this->email, self::EMAIL_VERIFICATION_CODE_CACHE_KEY), $code, 600); // 10 минут
        $cache->set(str_replace('{email}', $this->email, self::EMAIL_VERIFICATION_CODE_EXPIRE_CACHE_KEY), time() + 600, 600); // 10 минут

        return true;
    }

    /**
     * Генерация кода подтверждения, состоящего из шести цифр.
     *
     * @return string Код подтверждения в формате XXX-XXX.
     */
    protected function generateVerificationCode()
    {
        $randomNumber = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        return substr($randomNumber, 0, 3) . '-' . substr($randomNumber, 3, 3);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lastName' => Yii::t('auth.main', 'Фамилия'),
            'firstName' => Yii::t('auth.main', 'Имя'),
            'middleName' => Yii::t('auth.main', 'Отчество (при наличии)'),
            'email' => Yii::t('auth.main', 'Адрес электронной почты'),
            'emailVerificationCode' => Yii::t('auth.main', 'Код подтверждения'),
            'phone' => Yii::t('auth.main', 'Номер мобильного телефона'),
            'agreeTerms' => Yii::t('auth.main', 'Я подтверждаю свое согласие со всеми вышеперечисленными пунктами'),
            'agreeUserAgreement' => Yii::t('auth.main', 'С пользовательским соглашением ознакомлен(-а) и согласен(-на)'),
            'agreePrivacyPolicy' => Yii::t('auth.main', 'С политикой конфиденциальности ознакомлен(-а) и согласен(-на)'),
            'password' => Yii::t('auth.main', 'Укажите пароль'),
            'passwordRepeat' => Yii::t('auth.main', 'Укажите пароль (повтор)'),
            'captcha' => Yii::t('auth.main', 'Капча'),
        ];
    }

    /**
     * Возвращает данные для регистрации.
     *
     * Этот метод возвращает данные, необходимые для регистрации нового пользователя
     * в системе.
     *
     * @return array Данные для регистрации.
     */
    public function getRegisterData()
    {
        return $this->attributes;
    }
}