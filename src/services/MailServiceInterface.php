<?php

namespace vs\yii2\auth\services;

use vs\yii2\auth\exceptions\MailServiceException;

/**
 * Интерфейс MailServiceInterface.
 * 
 * Интерфейс для реализации сервиса отправки писем.
 */
interface MailServiceInterface
{
    /**
     * Отправляет письмо с указанным содержимым.
     * 
     * @param string $subject Тема письма.
     * @param string $from Отправитель письма.
     * @param string $to Получатель письма.
     * @param string $template Шаблон письма.
     * @param array|null $data Дополнительные данные для шаблона.
     * @throws MailServiceException Если отправка письма не удалась.
     */
    public function sendEmail($subject, $from, $to, $template, $data = null);
}