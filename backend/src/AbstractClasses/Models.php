<?php

namespace App\AbstractClasses;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

abstract class Models
{
    protected ?Entity $entity;
    protected ManagerRegistry $doctrine;
    protected EntityManagerInterface $em;
    protected ClassMetadata $metaDataEntity;
    protected array $entityStructure;
    protected bool $auto_flush;

    protected array $errors = [];

    /**
     * @throws Exception
     */
    public function __construct(?Entity $entity, ManagerRegistry $doctrine, bool $auto_flush = true)
    {
        $this->entity = $entity;
        $this->doctrine = $doctrine;

        // Получаем менеджер сущностей
        $em = $doctrine->getManager();

        // Проверяем, что это EntityManagerInterface
        if (!$em instanceof EntityManagerInterface) {
            throw new \LogicException('Expected EntityManagerInterface, got ' . get_class($em));
        }

        // Присваиваем EntityManager
        $this->em = $em;

        // Получаем метаданные сущности
        $this->metaDataEntity = $this->em->getClassMetadata(get_class($entity));

        // Структура сущности
        $this->entityStructure = $entity->getEntityStructure();

        // Автофлеш
        $this->auto_flush = $auto_flush;
    }

    private function setParam(string $param_name, $value): void
    {
        $method_name = 'set' . $this->composePropertyName($param_name);

        if (method_exists($this->entity, $method_name)) {
            $this->entity->$method_name($value);
        }
    }

    private function composePropertyName(string $param_name, bool $lcfirst = false): array|string
    {
        $param_name = str_replace('_', ' ', $param_name);
        $param_name = ucwords($param_name);
        $param_name = str_replace(' ', '', $param_name);
        return $lcfirst ? lcfirst($param_name) : $param_name;
    }

    /**
     * @throws Exception
     */
    public function saveModel(array $data, bool $is_new = false): ?Entity
    {
        if (empty($this->entity)) {
            throw new Exception('Entity is equal to null.');
        }

        $errors = self::validate($data, $this->entityStructure);
        $this->errors = array_merge_recursive($this->errors, $errors);

        if (!empty($this->errors)
            || !$this->validateAdditionally($data)
            || !$this->fillWithEntities($data)
            || !$this->validateEntites($data)) {
            throw new Exception($this->getErrorsMessage(), 400);
        }

        $entity_params = $this->entity->getEntityStructure(true);
        foreach ($entity_params as $param) {
            if (array_key_exists($param, $data)) {
                $this->setParam($param, $data[$param]);
            }
        }

        if ($is_new) {
            $this->em->persist($this->entity);
        }

        if ($this->auto_flush) {
            $this->em->flush();
        }

        return $this->entity;
    }

    public static function validate(array $data, array $entityStructure): array
    {
        $errors = [];

        foreach ($entityStructure as $field) {
            if ($field['show']) {
                if (isset($field['required']) && $field['required'] &&
                    (!array_key_exists($field['key'], $data) || !isset($data[$field['key']]))) {
                    $errors['required'][$field['key']] = $field['label'];
                } elseif (array_key_exists($field['key'], $data)
                    && !self::validateType($field['type'], $data[$field['key']])) {
                    $errors['type'][$field['key']] = $field['label'];
                }
            }
        }

        return $errors;
    }

    private static function validateType(string $type, mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        return match ($type) {
            'int', 'integer', 'entity' => is_int($value),
            'float' => is_float($value) || is_int($value),
            'boolean' => is_bool($value),
            'string' => is_string($value),
            'collection', 'array' => is_array($value),
            default => true,
        };
    }

    public static function convertTypesData($data, $entityStructure): array
    {
        foreach ($entityStructure as $key => $item) {
            if (array_key_exists($key, $data)) {
                switch ($item['type']) {
                    case 'int':
                    case 'integer':
                        $data[$key] = is_numeric($data[$key]) ? intval($data[$key]) : $data[$key];
                        break;
                    case 'float':
                        $data[$key] = is_numeric($data[$key]) ? floatval($data[$key]) : $data[$key];
                        break;
                }
            }
        }

        return $data;
    }

    protected abstract function validateAdditionally(array $data): bool;

    private function fillWithEntities(array &$data): bool
    {
        foreach ($data as $key => $value) {
            $param_name = $this->composePropertyName($key, true);
            if (
                array_key_exists($param_name, $this->metaDataEntity->associationMappings)
                && ((is_numeric($value) && $value > 0) || (is_array($value) && !empty($value)))
            ) {
                $className = $this->metaDataEntity->associationMappings[$param_name]['targetEntity'];

                if ($this->entityStructure[$param_name]['type'] === 'entity') {
                    $data[$param_name] = $this->em->getRepository($className)->find($value);
                } elseif ($this->entityStructure[$param_name]['type'] === 'collection') {
                    $qb = $this->em->createQueryBuilder();
                    $qb->select('e')
                        ->from($className, 'e')
                        ->where($qb->expr()->in('e.id', ':ids'))
                        ->setParameter('ids', $value);
                    $data[$param_name] = $qb->getQuery()->getResult();
                }

                if (!$data[$param_name]) {
                    $this->errors['entity'][$param_name] = $this->entityStructure[$param_name]['label'];
                }
            }
        }

        return empty($this->errors);
    }

    protected abstract function validateEntites(array $data): bool;

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorsMessage(): string
    {
        $message = '';
        if (isset($this->errors['required'])) {
            $message .= 'Не указаны следующие обязательные поля: <br>' . implode(', <br>', $this->errors['required']) . '<br>';
        }
        if (isset($this->errors['type'])) {
            $message .= 'Следующие поля не соответствуют необходимым типам: <br>' . implode(', <br>', $this->errors['type']) . '<br>';
        }
        if (isset($this->errors['entity'])) {
            $message .= 'Сущности по следующим полям не были найдены в базе данных: <br>' . implode(', <br>', $this->errors['entity']) . '<br>';
        }
        if (isset($this->errors['custom'])) {
            $message .= implode(', <br>', $this->errors['custom']);
        }

        return $message;
    }
}
