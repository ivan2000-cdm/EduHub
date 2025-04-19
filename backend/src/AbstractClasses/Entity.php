<?php

namespace App\AbstractClasses;

use App\Entity\CustomExtensions\EntityStructure;
use App\Interfaces\EntityInterface;
use Exception;

abstract class Entity implements EntityInterface
{
    protected ?EntityStructure $managerEntityStructure = null;

    /**
     * Получить структуру сущности
     *
     * @param bool $flag Если true, возвращаются только ключи сущности
     * @param bool $exclude_field Если true, исключаются определенные поля
     * @return array Структура сущности
     * @throws Exception
     */
    public function getEntityStructure(bool $flag = false, bool $exclude_field = false): array
    {
        return $this->getManagerEntityStructure()->getEntityStructure($flag, $exclude_field);
    }

    /**
     * Получить менеджера по управлении структуры сущности
     *
     * @return EntityStructure
     * @throws Exception
     */
    public function getManagerEntityStructure(): EntityStructure
    {
        if (!$this->managerEntityStructure) {
            $this->initManagerEntityStructure();
        }
        return $this->managerEntityStructure;
    }

    /**
     * Инициировать менеджера структуры сущности
     * @throws Exception
     */
    protected function initManagerEntityStructure(): void
    {
        if (!$this->managerEntityStructure) {
            $structure = $this->getStructure();

            $this->managerEntityStructure = new EntityStructure($structure);
            $this->managerEntityStructure->setExcludedFields($this->getExcludedFields());
        }
    }

    /**
     * Получить первичную структуру сущности
     *
     * @return array
     */
    protected abstract function getStructure(): array;

    /**
     * Получить исключаемые поля структуры сущности
     *
     * Переопределять в наследнике при необходимости
     *
     * @return string[] Формат [$keyExcludedField => $keyExcludedField, ...]
     */
    protected function getExcludedFields(): array {
        return [];
    }

    /**
     * Получить идентификатор сущности
     *
     * Этот метод должен быть реализован в каждом наследнике класса Entity.
     *
     * @return mixed
     */
    public abstract function getId();
}
