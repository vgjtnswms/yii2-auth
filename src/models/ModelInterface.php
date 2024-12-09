<?php

namespace vs\yii2\auth\models;

/**
 * Interface ModelInterface.
 * 
 * Интерфейс для реализации логики модели.
 */
interface ModelInterface
{
    /**
     * Устанавливает сценарий для модели.
     * 
     * @param string $scenario Название сценария.
     * @return void
     */
    public function setScenario($scenario);

    /**
     * Сохраняет текущую модель в базу данных.
     * 
     * @param bool $runValidation Нужно ли выполнять валидацию перед сохранением.
     * @param array|null $attributeNames Список атрибутов, которые должны быть сохранены. Если null, сохраняются все атрибуты.
     * @return bool Возвращает true, если сохранение прошло успешно.
     */
    public function save($runValidation = true, $attributeNames = null);
}