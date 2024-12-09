<?php

namespace vs\yii2\auth\services\authService;

use Yii;
use vs\yii2\auth\forms\LoginFormInterface;
use vs\yii2\auth\models\AuthCredentialsInterface;
use vs\yii2\auth\exceptions\auth\AuthServiceException;
use vs\yii2\auth\exceptions\auth\UserNotFoundException;
use vs\yii2\auth\exceptions\auth\LocalAuthServiceException;
use yii\base\Exception;

/**
 * Сервис авторизации пользователя по текущей (локальной) базе данных
 */
class LocalAuthService implements AuthServiceInterface
{
    /**
     * Авторизация в локальной базе
     *
     * @param AuthResourceInterface $authResource
     *
     * @return bool
     * @throws AuthServiceException
     * @throws Exception
     */
    public function auth(AuthResourceInterface $authResource)
    {
        if ($authResource instanceof LoginFormInterface) {
            return $this->authByLoginForm($authResource);
        }

        throw new AuthServiceException(Yii::t('auth.main', 'Не поддерживаемый метод авторизации.'));
    }

    /**
     * Авторизация по паролю и логину
     *
     * @param LoginFormInterface $loginForm
     *
     * @return bool
     * @throws AuthServiceException
     */
    protected function authByLoginForm(LoginFormInterface $loginForm)
    {
        $this->validateForm($loginForm);

        $credentials = $this->searchCredentials($loginForm);

        $this->validatePassword($credentials, $loginForm);
        $this->checkUser($credentials);

        Yii::$app->user->login($credentials->getUser(), $loginForm->getRememberMeDuration());
        return true;
    }

    /**
     * @param LoginFormInterface $loginForm
     *
     * @throws AuthServiceException
     */
    protected function validateForm(LoginFormInterface $loginForm)
    {
        if (!$loginForm->validate()) {
            Yii::info([
                'class' => self::class,
                'message' => 'AuthServiceException: Неверный логин или пароль.',
                'user' => $loginForm->username,
            ], 'auth.authorization');
            throw new AuthServiceException(Yii::t('auth.main', 'Неверный логин или пароль.'));
        }
    }

    /**
     * @param LoginFormInterface $loginForm
     *
     * @return AuthCredentialsInterface
     *
     * @throws AuthServiceException
     */
    protected function searchCredentials(LoginFormInterface $loginForm)
    {
        $authCredentialsClass = Yii::$container->getDefinitions()[AuthCredentialsInterface::class]['class'];
        if (!class_exists($authCredentialsClass)) {
            Yii::info([
                'class' => self::class,
                'message' => 'AuthServiceException: Сервис временно недоступен, попробуйте позже.',
                'user' => $loginForm->getUsername(),
                'authCredentialsClass' => $authCredentialsClass
            ], 'auth.authorization');
            throw new AuthServiceException(Yii::t('auth.main', 'Сервис временно недоступен, попробуйте позже.'));
        }

        /** @var AuthCredentialsInterface $authCredentials */
        $authCredentials = $authCredentialsClass::find()
            ->andWhere('lower(credential) = :username', [
                ':username' => strtolower(trim($loginForm->getUsername()))
            ])
            ->one();

        if (!$authCredentials) {
            Yii::info([
                'class' => self::class,
                'message' => 'UserNotFoundException: Пользователь не найден.',
                'user' => $loginForm->getUsername(),
            ], 'auth.authorization');
            throw new UserNotFoundException(Yii::t('auth.main', 'Пользователь не найден.'));
        }

        return $authCredentials;
    }

    /**
     * @param AuthCredentialsInterface $credentials
     * @param LoginFormInterface $loginForm
     *
     * @throws LocalAuthServiceException
     */
    protected function validatePassword(AuthCredentialsInterface $credentials, LoginFormInterface $loginForm)
    {
        $isValidHash = Yii::$app->security->validatePassword($loginForm->getPassword(), $credentials->validation);
        if (!$isValidHash) {
            Yii::info([
                'class' => self::class,
                'message' => 'LocalAuthServiceException: Неверный логин или пароль.',
                'user' => $loginForm->username,
            ], 'auth.authorization');
            throw new LocalAuthServiceException(Yii::t('auth.main', 'Неверный логин или пароль.'));
        }
    }

    /**
     * Проверяет, удален ли пользователь.
     *
     * @param AuthCredentialsInterface $credentials Объект, представляющий личность пользователя.
     *
     * @throws LocalAuthServiceException Если пользователь не найден или удален.
     */
    protected function checkUser(AuthCredentialsInterface $credentials)
    {
        if (!$credentials->getUser()) {
            Yii::info([
                'class' => self::class,
                'message' => 'LocalAuthServiceException: Ваша учетная запись не найдена. Пожалуйста, обратитесь к администратору.',
                'user' => $credentials->credential,
            ], 'auth.authorization');
            throw new LocalAuthServiceException(Yii::t('auth.main', 'Ваша учетная запись не найдена. Пожалуйста, обратитесь к администратору.'));
        }

        if ($credentials->getUser()->isDeleted()) {
            Yii::info([
                'class' => self::class,
                'message' => 'LocalAuthServiceException: Ваша учетная запись удалена. Пожалуйста, обратитесь к администратору.',
                'user' => $credentials->credential,
            ], 'auth.authorization');
            throw new LocalAuthServiceException(Yii::t('auth.main', 'Ваша учетная запись удалена. Пожалуйста, обратитесь к администратору.'));
        }
    }
}