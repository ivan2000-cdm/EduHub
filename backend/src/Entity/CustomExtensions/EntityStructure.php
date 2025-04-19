<?php

namespace App\Entity\CustomExtensions;

use Exception;

class EntityStructure
{
    // Конечная структура сущности
    protected array $entityStructure = [];

    // Первичная структура сущности
    protected array $entityStructureOrigin = [];

    // Исключающие поля (необходимо для обратной совместимости,
    // лучше производить операции по удалении и добавлении с конечной структурой сущности)
    protected array $excludedFields = [];

    // Структура поля сущности
    protected array $fieldStructure = [
        'label' => ['validator' => 'is_string'],
        'required' => ['validator' => 'is_bool', 'default' => false],
        'readonly' => ['validator' => 'is_bool', 'default' => false],
        'show' => ['validator' => 'is_bool', 'default' => false],
        'type' => ['validator' => 'is_string', 'default' => 'string'],
        'key' => ['validator' => 'is_string'],
        'sort' => ['validator' => 'is_validSort', 'default' => null]
    ];

    /**
     * EntityStructure constructor.
     * @param array $structure
     * @throws Exception
     */
    public function __construct(array $structure)
    {
        $this->setFields($structure);
        $this->entityStructureOrigin = $this->entityStructure;
    }

    /**
     * Сбросить структуру сущности до начальной
     *
     * @return $this
     */
    public function restore(): EntityStructure
    {
        $this->entityStructure = $this->entityStructureOrigin;
        return $this;
    }

    /**
     * Изменение параметра структуры поля
     *
     * @param string $key
     * @param array  $params
     *
     * @return $this
     * @throws Exception
     */
    public function changeParam(string $key, array $params): self {
        $field = $this->get($key);
        foreach ($params as $param => $value) {
            $field[$param] = $value;
        }

        $this->set($field);

        return $this;
    }

    /**
     * Установить поля, которые нужно исключить из структуры сущности
     *
     * @param string[] $fields
     * @return $this
     */
    public function setExcludedFields(array $fields): EntityStructure
    {
        $this->excludedFields = $fields;
        return $this;
    }

    /**
     * Добавить исключающие поля
     *
     * @param string[] $fields
     * @return $this
     */
    public function addExcludedFields(array $fields): EntityStructure
    {
        $this->excludedFields = array_unique(array_merge($this->excludedFields, $fields));
        return $this;
    }

    /**
     * Добавить исключающее поле
     *
     * @param string $field
     * @return $this
     */
    public function addExcludedField(string $field): EntityStructure
    {
        $this->excludedFields[] = $field;
        return $this;
    }

    /**
     * Удалить исключающее поле
     *
     * @param string $key
     * @return $this
     */
    public function removeExcludedField(string $key): EntityStructure
    {
        unset($this->excludedFields[$key]);
        return $this;
    }

    /**
     * Удалить исключающие поля
     *
     * @param string[] $keys
     * @return $this
     */
    public function removeExcludedFields(array $keys): EntityStructure
    {
        foreach ($keys as $key) {
            $this->removeField($key);
        }
        return $this;
    }

    /**
     * Добавить поле сущности
     *
     * @param string[] $field
     * @return $this
     * @throws Exception
     */
    public function set(array $field): EntityStructure
    {
        $field = $this->prepareField($field);
        $this->entityStructure[$field['key']] = $field;
        return $this;
    }

    /**
     * Добавить поля сущности
     *
     * @param array $fields
     * @return $this
     * @throws Exception
     */
    public function setFields(array $fields): EntityStructure
    {
        foreach ($fields as $field) {
            $this->set($field);
        }
        return $this;
    }

    /**
     * Удалить поле сущности
     *
     * @param string $key
     * @return $this
     */
    public function removeField(string $key): EntityStructure
    {
        unset($this->entityStructure[$key]);
        return $this;
    }

    /**
     * Удалить поля сущности
     *
     * @param string[] $keys
     * @return $this
     */
    public function removeFields(array $keys): EntityStructure
    {
        foreach ($keys as $key) {
            $this->removeField($key);
        }
        return $this;
    }

    /**
     * Получить поле сущности по ключу
     *
     * @param string $key
     * @return string[]
     */
    public function get(string $key): array
    {
        return $this->entityStructure[$key];
    }

    /**
     * Получить поля сущности по набору ключей
     *
     * @param array $keys
     * @param bool $only_keys
     * @return string[]|array
     */
    public function getByKeys(array $keys, bool $only_keys = false): array
    {
        $result = array_filter(
            $this->entityStructure,
            function ($field) use ($keys) {
                return in_array($field['key'], $keys);
            }
        );

        return $only_keys ? array_keys($result) : $result;
    }

    /**
     * Получить все поля сущности из конечной структуры сущности
     *
     * @param bool $only_keys
     * @return string[]|array
     */
    public function getAll(bool $only_keys = false): array
    {
        return $only_keys ? array_keys($this->entityStructure) : $this->entityStructure;
    }

    /**
     * Получить поля сущности (нужно для обратной совместимости)
     *
     * @param bool $only_keys
     * @param bool $exclude_field
     * @return string[]|array
     */
    public function getEntityStructure(bool $only_keys = false, bool $exclude_field = false): array
    {
        $result = $exclude_field
            ? array_diff_key($this->entityStructure, $this->excludedFields)
            : $this->entityStructure;

        return $only_keys ? array_keys($result) : $result;
    }

    /**
     * Получить все поля сущности удовлетворяющие запросу по одному параметру
     *
     * @param string $key
     * @param $value
     * @param bool $only_keys
     * @return string[]|array
     */
    public function getByParam(string $key, $value, bool $only_keys = false): array
    {
        $result = array_filter(
            $this->entityStructure,
            function ($field) use ($key, $value) {
                return $field[$key] === $value;
            }
        );

        return $only_keys ? array_keys($result) : $result;
    }

    /**
     * Получить все поля сущности удовлетворяющие запросу по нескольким параметрам
     *
     * @param array $search
     * @param bool $only_keys
     * @return string[]|array
     */
    public function getByParams(array $search, bool $only_keys = false): array
    {
        $result = array_filter(
            $this->entityStructure,
            function ($field) use ($search) {
                return array_intersect_assoc($search, $field);
            }
        );

        return $only_keys ? array_keys($result) : $result;
    }

    /**
     * Получить все обязательные поля сущности
     *
     * @param bool $only_keys
     * @return array|string[]
     */
    public function getRequired(bool $only_keys = false): array
    {
        return $this->getByParam('required', true, $only_keys);
    }

    /**
     * Получить все поля-сущности
     *
     * @param bool $only_keys
     * @return array|string[]
     */
    public function getTypeEntity(bool $only_keys = false): array
    {
        return $this->getByParam('type', 'entity', $only_keys);
    }

    /**
     * Заполнение полей значениями по умолчанию и проверка на валидность
     *
     * @param array $field
     * @return array
     * @throws Exception
     */
    public function prepareField(array $field): array
    {
        foreach ($this->fieldStructure as $key => $structure) {
            if (!key_exists($key, $field)) {
                if (key_exists('default', $structure)) {
                    $field[$key] = $structure['default'];
                } else {
                    throw new Exception('Структура поля не соответствует ожидаемой!');
                }
            }

            $this->validate($key, $field);
        }

        return $field;
    }

    /**
     * Проверить поля сущности на соответствие необходимой структуре и тупу полей структуры сущности
     *
     * @param string $key
     * @param array $field
     * @throws Exception
     */
    public function validate(string $key, array $field): void
    {
        $validateFunction = $this->fieldStructure[$key]['validator'];
        if (function_exists($validateFunction)) {
            if (!$validateFunction($field[$key])) {
                throw new Exception('Тип структуры поля не соответствует ожидаемому!');
            }
        } else if (method_exists($this, $validateFunction)) {
            if (!$this->$validateFunction($field[$key])) {
                throw new Exception('Тип структуры поля не соответствует ожидаемому!');
            }
        } else {
            throw new Exception('Используемая функция валидации не существует!');
        }
    }

    /**
     * Проверка на валидность поля сортировки
     *
     * @param string|null $value
     * @return bool
     */
    public function is_validSort(?string $value): bool
    {
        return is_null($value) || in_array(strtolower($value), ['asc', 'desc']);
    }
}
