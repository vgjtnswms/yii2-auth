<?php

namespace vs\yii2\auth\services\authService;

use Yii;
use vs\yii2\auth\services\authService\AuthResourceInterface;
use vs\yii2\auth\exceptions\auth\AuthServiceException;
use vs\yii2\auth\exceptions\auth\UserNotFoundException;
use vs\yii2\auth\exceptions\auth\LocalAuthServiceException;

class CompositeAuthService implements AuthServiceInterface
{
    /**
     * @param AuthResourceInterface $authResource
     *
     * @return bool
     * @throws AuthServiceException
     */
    public function auth(AuthResourceInterface $authResource)
    {
        $authServices = [
            LocalAuthService::class,
            // Другие системы
        ];

        foreach ($authServices as $authServiceClass) {
            try {
                /** @var AuthResourceInterface $authService */
                $authService = new $authServiceClass();
                $auth = $authService->auth($authResource);

                if ($auth) {
                    return true;
                }
            } catch (UserNotFoundException | LocalAuthServiceException $e) {
                continue;
            } catch (AuthServiceException $e) {
                throw $e;
            }
        }

        throw new AuthServiceException(Yii::t('auth.main', 'Неверный логин или пароль.'));
    }
}