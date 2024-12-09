<?php

namespace vs\yii2\auth\services;

use Yii;
use vs\yii2\auth\exceptions\MailServiceException;
use Exception;

/**
 * Класс MailService.
 * 
 * Реализация интерфейса MailServiceInterface для отправки писем.
 */
class MailService implements MailServiceInterface
{
    /**
     * Логируем письмо.
     *
     * {@inheritDoc}
     */
    public function sendEmail($subject, $from, $to, $template, $data = null)
    {
        try {
            $content = Yii::$app->mailer->render($template, $data);

            $result = Yii::$app->mailer->compose()
                ->setTo($to)
                ->setSubject(Yii::t('auth.main', 'Код подтверждения'))
                ->setHtmlBody($content)
                ->send();

            if ($result) {
                return;
            }

        } catch (Exception $e) {}

        Yii::info([
            'class' => self::class,
            'message' => 'MailServiceException',
            'subject' => $subject,
            'from' => $from,
            'to' => $to,
            'template' => $template,
            'data' => $data,
            'exceptionMessage' => $e->getMessage() ?? null,
            'trace' => $e->getTraceAsString() ?? null,
        ], 'auth.registration');
        throw new MailServiceException(Yii::t('auth.main', 'Не удалось отправить письмо на почту.'));
    }

}