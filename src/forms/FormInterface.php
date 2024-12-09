<?php

namespace vs\yii2\auth\forms;

/**
 * Interface FormInterface.
 * 
 * Интерфейс для реализации логики формы.
 */
interface FormInterface
{
    /**
     * Загружает данные в модель формы.
     * 
     * @param array $data Данные для загрузки в модель.
     * @param string|null $formName Имя формы. Если null, используются все данные.
     * @return bool Возвращает true, если данные были загружены успешно.
     */
    public function load($data, $formName = null);

    /**
     * Проверяет, соответствует ли текущая модель правилам валидации.
     * 
     * @param array|null $attributeNames Массив имен атрибутов для проверки. Если null, проверяются все атрибуты.
     * @param bool $clearErrors Очищать ли предыдущие ошибки перед валидацией.
     * @return bool Возвращает true, если модель прошла валидацию.
     */
    public function validate($attributeNames = null, $clearErrors = true);

    /**
     * Возвращает значения атрибутов модели.
     * 
     * @param array|null $names Массив имен атрибутов для извлечения. Если null, возвращаются все атрибуты.
     * @param array $except Массив имен атрибутов, которые не должны возвращаться.
     * @return array Возвращает массив значений атрибутов.
     */
    public function getAttributes($names = null, $except = []);

    /**
     * Добавляет ошибку в модель.
     * 
     * @param string $attribute Имя атрибута, к которому относится ошибка.
     * @param string $error Сообщение об ошибке.
     * @return void
     */
    public function addError($attribute, $error);

    /**
     * Проверяет, есть ли ошибки в модели.
     * 
     * @param string|null $attribute Имя атрибута для проверки. Если null, проверяются все атрибуты.
     * @return bool Возвращает true, если есть ошибки, иначе false.
     */
    public function hasErrors($attribute = null);

    /**
     * Возвращает массив ошибок, связанных с атрибутами модели.
     * 
     * @return array Ассоциативный массив, где ключами являются имена атрибутов, а значениями — массивы с ошибками.
     */
    public function getErrors();

    /**
     * Добавляет несколько ошибок в модель.
     * 
     * @param array $errors Ассоциативный массив ошибок, где ключами являются имена атрибутов, а значениями — массивы с ошибками.
     */
    public function addErrors(array $errors);

    /**
     * Устанавливает сценарий для модели.
     * 
     * @param string $scenario Название сценария.
     * @return void
     */
    public function setScenario($scenario);
}