<?php

namespace vs\yii2\auth\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\web\IdentityInterface;
use vs\yii2\auth\forms\FormInterface;
use vs\yii2\auth\forms\LoginFormInterface;
use vs\yii2\auth\forms\RegistrationFormInterface;
use vs\yii2\auth\forms\PasswordRecoveryFormInterface;
use vs\yii2\auth\services\authService\AuthServiceInterface;
use vs\yii2\auth\services\passwordRecoveryService\PasswordRecoveryServiceInterface;
use vs\yii2\auth\services\registerService\RegisterServiceInterface;
use vs\yii2\auth\exceptions\auth\AuthServiceException;
use vs\yii2\auth\exceptions\PasswordRecoveryServiceException;
use vs\yii2\auth\exceptions\RegisterServiceException;
use Exception;
use yii\base\InvalidConfigException;

/**
 * Undocumented class
 */
class AuthController extends Controller
{
    /**
     * @var string
     */
    public $loginView = '@auth/views/login';

    /**
     * @var string
     */
    public $registrationView = '@auth/views/registration';

    /**
     * @var string
     */
    public $restoreView = '@auth/views/restore';

    /**
     * @var string
     */
    public $resetPasswordView = '@auth/views/reset-password';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setViewPath('@auth/views');

        if (!Yii::$app->has('session')) {
            throw new InvalidConfigException('Component "session" is not configured.');
        }

        if (!Yii::$app->has('user')) {
            throw new InvalidConfigException('Component "user" is not configured.');
        }

        if (!Yii::$app->has('cache')) {
            throw new InvalidConfigException('Component "cache" is not configured.');
        }

        if (!Yii::$app->has('security')) {
            throw new InvalidConfigException('Component "security" is not configured.');
        }

        if (!Yii::$app->has('i18n')) {
            throw new InvalidConfigException('Component "i18n" is not configured.');
        }
        // TODO: проеверять что в конфиге указан auth.main.php

        if (!Yii::$app->has('reCaptcha')) {
            throw new InvalidConfigException('Component "reCaptcha" is not configured.');
        }

        if (!Yii::$app->has('mailer')) {
            throw new InvalidConfigException('Component "mailer" is not configured.');
        }

        if (!Yii::$app->has('authService')) {
            throw new InvalidConfigException('Service "authService" is not configured.');
        }

        if (!Yii::$app->has('passwordRecoveryService')) {
            throw new InvalidConfigException('Service "passwordRecoveryService" is not configured.');
        }

        if (Yii::$container->has(LoginFormInterface::class) === false) {
            throw new InvalidConfigException('Форма «'.LoginFormInterface::class.'» не настроена. Пожалуйста, убедитесь, что она указана в конфигурации.');
        }

        if (Yii::$container->has(PasswordRecoveryFormInterface::class) === false) {
            throw new InvalidConfigException('Форма «'.PasswordRecoveryFormInterface::class.'» не настроена. Пожалуйста, убедитесь, что она указана в конфигурации.');
        }

        if (Yii::$container->has(RegistrationFormInterface::class) === false) {
            throw new InvalidConfigException('Форма «'.RegistrationFormInterface::class.'» не настроена. Пожалуйста, убедитесь, что она указана в конфигурации.');
        }
    }

    /**
     * Выполняет аутентификацию пользователя и перенаправляет его на главную страницу или указанную URL.
     *
     * Этот метод проверяет, является ли текущий пользователь гостем. Если пользователь уже аутентифицирован,
     * происходит перенаправление на главную страницу.
     *
     * Если пользователь является гостем, создается новый экземпляр `LoginForm`. Метод загружает данные из POST-запроса
     * и выполняет валидацию модели. Если данные корректны, метод выполняет аутентификацию через сервис `authService`.
     *
     * В случае успешной аутентификации сбрасываются попытки аутентификации и происходит перенаправление на главную
     * страницу или на URL, указанный в параметре `redirect`. В случае ошибки аутентификации, ошибка добавляется в модель
     * и увеличивается счетчик попыток аутентификации.
     *
     * Метод отображает представление `login` с моделью `LoginForm`.
     *
     * @return string|Response Строка, представляющая результат рендеринга представления, или объект ответа для перенаправления.
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
			$this->goHome();
		}

        /** @var LoginFormInterface $model */
        $model = Yii::$container->get(LoginFormInterface::class);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                /** @var AuthServiceInterface $authService */
                $authService = Yii::$app->authService;
                $authService->auth($model);

                $model->resetAuthAttempts();
                Yii::$app->request->get('redirect') 
                    ? $this->redirect(Yii::$app->request->get('redirect')) 
                    : $this->goHome();
            } catch (AuthServiceException $e) {
                $model->addError('login-form', $e->getMessage());
                $model->incrementAuthAttempts();
            } catch (Exception $e) {
                Yii::error([
                    'class' => self::class,
                    'method' => 'actionLogin',
                    'message' => 'Exception: Сервис временно недоступен, попробуйте позже.',
                    'model' => $model,
                    'exceptionMessage' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ], 'auth.registration');
                $model->addError('login-form', Yii::t('auth.main', 'Сервис временно недоступен, попробуйте позже.'));
                $model->incrementAuthAttempts();
            }
        }

        return $this->renderAction(
            $model,
            Url::toRoute(['/auth/auth/login']),
            $this->loginView
        );
    }

    /**
     * Выход текущего пользователя и перенаправление его на главную страницу.
     *
     * @return Response Объект ответа для перенаправления.
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Восстановление пароля. Шаг 1.
     *
     * Метод загружает данные из формы, проверяет их и, если данные валидны, генерирует и 
     * отправляет ссылку для восстановления пароля на электронную почту пользователя.
     *
     * @return string|Response Строка, представляющая результат рендеринга представления, или объект ответа для перенаправления.
     */
    public function actionRestore()
    {
        if (!Yii::$app->user->isGuest) {
			$this->goHome();
		}

        /** @var PasswordRecoveryFormInterface $model */
        $model = Yii::$container->get(PasswordRecoveryFormInterface::class);
        $model->setScenario(PasswordRecoveryFormInterface::SCENARIO_REQUEST);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                /** @var PasswordRecoveryServiceInterface $passwordRecoveryService */
                $passwordRecoveryService = Yii::$app->passwordRecoveryService;
                $passwordRecoveryService->generateAndSendRecoveryLink($model);

                Yii::$app->session->setFlash('success', Yii::t('auth.main', 'Письмо с инструкциями по восстановлению пароля отправлено на вашу электронную почту.'));
                return $this->goHome();
            } catch (PasswordRecoveryServiceException $e) {
                $model->addError('restore-form', $e->getMessage());
            } catch (Exception $e) {
                Yii::error([
                    'class' => self::class,
                    'method' => 'actionRestore',
                    'message' => 'Exception: Сервис временно недоступен, попробуйте позже.',
                    'model' => $model,
                    'exceptionMessage' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ], 'auth.registration');
                $model->addError('restore-form', Yii::t('auth.main', 'Сервис временно недоступен, попробуйте позже.'));
            }
        }

        return $this->renderAction(
            $model,
            Url::toRoute(['/auth/auth/restore']),
            $this->restoreView
        );
    }

    /**
     * Восстановление пароля. Шаг 2.
     *
     * Метод cбрасывает пароль.
     *
     * @return string|Response Строка, представляющая результат рендеринга представления, или объект ответа для перенаправления.
     */
    public function actionResetPassword()
    {
        if (!Yii::$app->user->isGuest) {
			$this->goHome();
		}

        /** @var PasswordRecoveryFormInterface $model */
        $model = Yii::$container->get(PasswordRecoveryFormInterface::class);
        $model->setScenario(PasswordRecoveryFormInterface::SCENARIO_RESET);
        $model->setHash(Yii::$app->request->get('fk'));

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                /** @var PasswordRecoveryServiceInterface $passwordRecoveryService */
                $passwordRecoveryService = Yii::$app->passwordRecoveryService;
                $result = $passwordRecoveryService->changePassword($model);

                if ($result instanceof IdentityInterface) {
                    Yii::$app->user->login($result);
                }
                Yii::$app->session->setFlash('success', Yii::t('auth.main', 'Ваш пароль был успешно изменен.'));
                return $this->goHome();
            } catch (PasswordRecoveryServiceException $e) {
                $model->addError('reset-password-form', $e->getMessage());
            } catch (Exception $e) {
                Yii::error([
                    'class' => self::class,
                    'method' => 'actionResetPassword',
                    'message' => 'Exception: Сервис временно недоступен, попробуйте позже.',
                    'model' => $model,
                    'exceptionMessage' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ], 'auth.registration');
                $model->addError('reset-password-form', Yii::t('auth.main', 'Сервис временно недоступен, попробуйте позже.'));
            }
        }

        return $this->renderAction(
            $model,
            Url::toRoute(['/auth/auth/reset-password', 'fk' => $model->getHash()]),
            $this->resetPasswordView
        );
    }

    /**
     * Действие для регистрации нового пользователя.
     *
     * Если пользователь уже авторизован, он будет перенаправлен на главную страницу.
     * Если форма регистрации отправлена и данные валидны, будет произведена регистрация пользователя
     * с помощью сервиса регистрации. В случае успеха пользователь будет перенаправлен на главную страницу.
     * В случае ошибки будет отображено сообщение об ошибке.
     *
     * @return string|Response Строка, представляющая результат рендеринга представления, или объект ответа для перенаправления.
     */
    public function actionRegistration()
    {
        if (!Yii::$app->user->isGuest) {
			$this->goHome();
		}

        /** @var RegistrationFormInterface $model */
        $model = Yii::$container->get(RegistrationFormInterface::class);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                /** @var RegisterServiceInterface $registerService */
                $registerService = Yii::$app->registerService;
                $registerService->register($model);

                /** @var LoginFormInterface $loginForm */
                $loginForm = Yii::$container->get(LoginFormInterface::class);
                $loginForm->setUsername($model->email);
                $loginForm->setPassword($model->password);
                $loginForm->setRememberMe(true);

                /** @var AuthServiceInterface $authService */
                $authService = Yii::$app->authService;
                $authService->auth($loginForm);

                return $this->goHome();
            } catch (RegisterServiceException $e) {
                $model->addError('registration-form', $e->getMessage());
            } catch (Exception $e) {
                Yii::error([
                    'class' => self::class,
                    'method' => 'actionRegistration',
                    'message' => 'Exception: Сервис временно недоступен, попробуйте позже.',
                    'model' => $model,
                    'exceptionMessage' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ], 'auth.registration');
                $model->addError('registration-form', Yii::t('auth.main', 'Сервис временно недоступен, попробуйте позже.'));
            }
        }

        return $this->renderAction(
            $model,
            Url::toRoute(['/auth/auth/registration']),
            $this->registrationView
        );
    }

    /**
     * Отправка кода подтверждения на указанный адрес электронной почты.
     *
     * Этот метод вызывается через AJAX и возвращает JSON-ответ с результатом операции.
     * Если указанный email не валиден, возвращается сообщение об ошибке валидации.
     * Если код подтверждения был успешно отправлен, возвращается сообщение об успехе.
     * В случае ошибки сервиса регистрации или другой ошибки возвращается сообщение об ошибке.
     *
     * @return array JSON-ответ с результатом операции
     */
    public function actionSendVerificationCode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /** @var RegistrationFormInterface $model */
        $model = Yii::$container->get(RegistrationFormInterface::class);
        $model->email = Yii::$app->request->post('email');

        if (!$model->validate(['email'])) {
            return ['success' => false, 'message' => $model->getErrors('email')[0]];
        }

        try {
            $model->sendEmailVerificationCode();
            return ['success' => true, 'message' => Yii::t('auth.main', 'Код подтверждения отправлен на вашу почту.')];
        } catch (RegisterServiceException $e) {
            Yii::info([
                'class' => self::class,
                'method' => 'actionSendVerificationCode',
                'message' => 'RegisterServiceException',
                'model' => $model,
                'exceptionMessage' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 'auth.registration');
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (Exception $e) {
            Yii::error([
                'class' => self::class,
                'method' => 'actionSendVerificationCode',
                'message' => 'Exception',
                'model' => $model,
                'exceptionMessage' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 'auth.registration');
        }

        return ['success' => false, 'message' => Yii::t('auth.main', 'Сервис временно недоступен, попробуйте позже.')];
    }

    /**
     * Обрабатывает ошибки формы и перенаправляет, если они есть.
     *
     * @param FormInterface $model
     * @param string $redirectRoute
     * @param string $view
     * @return string|Response Строка, представляющая результат рендеринга представления, или объект ответа для перенаправления.
     */
    protected function renderAction($model, $redirectRoute, $view)
    {
        if ($model->hasErrors()) {
            Yii::$app->session->setFlash(get_class($model).'Errors', $model->getErrors());
            Yii::$app->session->setFlash(get_class($model).'Attributes', $model->getAttributes());
            return $this->redirect($redirectRoute);
        }

        if (Yii::$app->session->hasFlash(get_class($model).'Errors')) {
            $sessionErrors = Yii::$app->session->getFlash(get_class($model).'Errors');
            $model->addErrors($sessionErrors);
        }

        if (Yii::$app->session->hasFlash(get_class($model).'Attributes')) {
            $attributes = Yii::$app->session->getFlash(get_class($model).'Attributes');
            $model->load($attributes, '');
        }

        return $this->render($view, [
            'model' => $model
        ]);
    }
}