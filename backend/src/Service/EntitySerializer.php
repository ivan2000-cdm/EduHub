<?php


namespace App\Service;


use App\AbstractClasses\Entity;
use App\Interfaces\EntityInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\PersistentCollection;
use Exception;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EntitySerializer
{
    private Serializer $serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * Трансформация из entity в ассоциативный массив
     *
     * @param object $entity
     * @param bool   $withEntityStructure
     *
     * @return array
     */
    public function entityToArray(object $entity, bool $withEntityStructure = true): array
    {
        $entity_structure = $entity->getEntityStructure(true, true);
        $data = $this->parseEntityToArray($entity_structure, $entity);
        if ($withEntityStructure) {
            $data['entityStructure'] = $entity->getEntityStructure(false, true);
        }

        return $data;
    }

    /**
     * Трансформация из entity в json объект
     *
     * @param object $entity
     *
     * @return string
     */
    public function entityToJson(object $entity): string
    {
        $data = $this->entityToArray($entity);
        return $this->arrayToJson($data);
    }

    /**
     * Трансформация из json объекта в entity формат.
     *
     * @param array  $json_entity
     * @param string $entity_name - наименование класса
     *
     * @return object
     */
    public function jsonToEntity(array $json_entity, string $entity_name): object
    {
        return $this->serializer->deserialize($json_entity, $entity_name, 'json');
    }

    /**
     * Трансформация коллекции Entities в json формат
     *
     * @param array $array
     *
     * @return false|string
     * @throws Exception
     */
    public function entityArrayToJson(array $array): false|string
    {
        return json_encode($this->collectionEntityToPrepareArray($array));
    }

    /**
     * Трансформация коллекции Entities в готовый массив
     *
     * @param array $array
     *
     * @return array
     * @throws Exception
     */
    public function collectionEntityToPrepareArray(array $array): array
    {
        // Если массив пустой, возвращаем штатный ответ
        if (empty($array)) {
            return [
                'timeToGet' => 0,
                'entityStructure' => [],
                'data' => []
            ];
        } elseif (!($array[0] instanceof EntityInterface)) {
            throw new Exception('Try to parse not entity object');
        }

        $entity_structure = $array[0]->getEntityStructure(true, true);
        if (empty($entity_structure)) {
            throw new Exception('Entity structure not formed');
        }

        $start_parsing = microtime(true);
        $result_arr = $this->collectionEntityToArray($array, $entity_structure);

        $entity_structure = $array[0]->getEntityStructure(false, true);
        $end_parsing = microtime(true) - $start_parsing;

        return [
            'timeToGet' => $end_parsing,
            'entityStructure' => $entity_structure,
            'data' => $result_arr
        ];
    }

    /**
     * @param array $array
     * @param array $structure
     * @return array
     * @throws Exception
     */
    public function collectionEntityToArraySpecific(array $array, array $structure): array
    {
        if (empty($array)) {
            throw new Exception('Empty');
        } elseif (!($array[0] instanceof EntityInterface)) {
            throw new Exception('Try to parse not entity object');
        } else if (empty($structure)) {
            throw new Exception('Entity structure not formed');
        }

        $start_parsing = microtime(true);
        $entity_structure = $this->getSpecificEntity($structure);
        $result_arr = $this->collectionEntityToArray($array, $entity_structure);
        $entity_structure = $structure;
        $end_parsing = microtime(true) - $start_parsing;

        return [
            'timeToGet' => $end_parsing,
            'entityStructure' => $entity_structure,
            'data' => $result_arr
        ];
    }

    /**
     * Трансформация коллекции Entities в массив
     *
     * @param array $array
     * @param array $entity_structure
     *
     * @return array
     */
    public function collectionEntityToArray(array $array, array $entity_structure): array
    {
        $result_arr = [];
        foreach ($array as $key => $item) {
            $entity_arr = $this->parseEntityToArray($entity_structure, $item);
            $result_arr[$key] = $entity_arr;
        }

        return $result_arr;
    }

    /**
     * Трансформация массива в json формат
     *
     * @param array $array
     *
     * @return string
     */
    public function arrayToJson(array $array): string
    {
        return $this->serializer->serialize($array, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
    }

    /**
     * Метод позволяет получить значение Entity
     *
     * @param string $param_name
     * @param Entity $obj
     * @param bool   $scalar Нужно ли возвращать скалярное значение или сущность
     *
     * @return mixed
     */
    public function getEntityParam(string $param_name, Entity &$obj, bool $scalar = false): mixed
    {
        $prefix = (!in_array($param_name, ['blocked', 'deleted', 'summer'])) ? 'get' : 'is';
        $method_name = $prefix . ucfirst($param_name);
        $value = $obj->$method_name();

        if ($scalar) {
            if ($value instanceof Entity) {
                return method_exists($value, 'getName') ? $value->getName() : $value->getId();
            }

            if ($value instanceof Collection) {
                return $value->map(
                    function (Entity $item) {
                        return method_exists($item, 'getName') ? $item->getName() : $item->getId();
                    }
                );
            }
        }

        return $value;
    }

    /**
     * Метод парсит полученный массив Entity.
     * Если он находит у свойства Entity relation на другую Entity, то парсит этот relation
     *
     * @param $entity_structure
     * @param $data
     *
     * @return array
     */
    public function parseEntityToArray($entity_structure, $data): array
    {
        $entity_arr = [];
        foreach ($entity_structure as $struct) {
            try {
                $elem = $this->getEntityParam($struct, $data);
                if (is_object($elem) and ($elem instanceof EntityInterface)) {
                    $elem_structure = $elem->getEntityStructure(true, true);
                    $entity_arr[$struct] = $this->parseEntityToArray($elem_structure, $elem);
                } elseif (is_object($elem) and ($elem instanceof PersistentCollection)) {
                    $elements_array = [];
                    $elem_persist_collection = $elem->getValues();
                    if (!empty($elem_persist_collection)) {
                        $child_struct = $elem_persist_collection[0]->getEntityStructure(false, true);
                        foreach ($elem_persist_collection as $child_elem) {
                            $elements_array[] = $this->parseEntityToArray(array_keys($child_struct), $child_elem);
                        }
                    } else {
                        $elements_array = [];
                        $child_struct = [];
                    }

                    $entity_elem_structured = ['data' => $elements_array, 'entityStructure' => $child_struct];
                    $entity_arr[$struct] = $entity_elem_structured;
                } elseif (is_array($elem) and !empty($elem[0]) and is_object($elem[0])) {
                    $elements_array = [];
                    $child_struct = $elem[0]->getEntityStructure(false, true);
                    foreach ($elem as $child_elem) {
                        $elements_array[] = $this->parseEntityToArray(array_keys($child_struct), $child_elem);
                    }

                    $entity_elem_structured = ['data' => $elements_array, 'entityStructure' => $child_struct];
                    $entity_arr[$struct] = $entity_elem_structured;
                } elseif (is_string($elem)) {
                    $entity_arr[$struct] = trim($elem);
                } else {
                    $entity_arr[$struct] = $elem;
                }
            } catch (EntityNotFoundException $e) {
                $entity_arr[$struct] = null;
            }
        }
        return $entity_arr;
    }

    /**
     * Метод, который позволяет получить реструктуризированный объект в соответствии с переданной структурой этого
     * объекта. Для получения значений из связанных сущностей объекта используется ключ pathToData
     *
     * @param $data
     * @param $structure
     *
     * @return array
     */
    public function getRestructurizedEntity($data, $structure): array
    {
        foreach ($structure as $key_name => $element) {
            $value = key_exists('pathToData', $element) ? $this->getValueFromItem(
                $data,
                $element['pathToData'],
                true
            ) : $this->getValueFromItem($data, $key_name);
            $new_data[$key_name] = $value;
        }

        foreach ($structure as $elem) {
            if (key_exists('pathToData', $elem)) {
                unset($elem['pathToData']);
            }
        }

        return $new_data;
    }

    /**
     * Получаем значение через структуру
     *
     * @param        $item
     * @param string $key
     * @param false $is_need_explode
     * @param string $delimeter
     *
     * @return mixed
     */
    protected function getValueFromItem($item, string $key, false $is_need_explode = false, string $delimeter = '.'): mixed
    {
        try {
            if ($is_need_explode) {
                $key = explode($delimeter, $key);
            }

            if (is_array($key)) {
                $count_keys = count($key) - 1;
                $value = $item;
                for ($i = 0; $i <= $count_keys; $i++) {
                    $method_name = 'get' . ucwords($key[$i]);
                    $value = $value->$method_name();
                    if (empty($value)) {
                        return '';
                    }
                }

                return $value;
            } else {
                $method_name = 'get' . ucwords($key);
                return $item->$method_name();
            }
        } catch (EntityNotFoundException $exception) {
            return '';
        }
    }

    /**
     * Получить поля сущности из списка
     *
     * @param array|Collection|Entity $entity        сущность
     * @param array                   $fields        список необходимых полей
     * @param bool                    $withStructure Добавлять ли entityStructure
     *
     * @return array
     * @throws Exception
     */
    public function getFieldsFromEntity(Entity|Collection|array $entity, array $fields, bool $withStructure = true): array
    {
        $result = [];

        // Если передана простая сущность
        if ($entity instanceof Entity) {
            // Перебираем необходимые поля и достаем по ним значения
            foreach ($fields as $key => $value) {
                // Если нет необходимости получения отдельных полей у вложенной сущности,
                // то достаем только скалярное значение или массив id в случае коллекции
                if (!is_array($value)) {
                    $result[$value] = $this->getEntityParam($value, $entity, true);
                    continue;
                }

                // Для вложенной сущности также достаем необходимые поля, если они указаны
                $item = $this->getEntityParam($key, $entity);
                $result[$key] = $this->getFieldsFromEntity($item, $value, false); // без $withStructure
            }

            if ($withStructure) {
                // Формируем структуру из необходимых полей
                $result['entityStructure'] = array_filter(
                    $entity->getEntityStructure(false, true),
                    function ($field) use ($fields) {
                        return in_array($field['key'], $fields);
                    }
                );
            }
        }

        // Если переданная коллекция сущностей
        if ($entity instanceof Collection || is_array($entity)) {
            // Проходимся по элементам коллекции и у них получаем необходимые поля
            /** @var Entity $item */
            foreach ($entity as $item) {
                $result['data'][] = $this->getFieldsFromEntity($item, $fields, false);
            }

            // Если есть хоть один элемент, то формируем структуру
            // @TODO Нужно подумать на тем, что будут случаи, когда коллекция будет пуста,
            // @TODO но структуру все равно формировать нужно будет
            if (isset($item) && $withStructure) {
                $result['entityStructure'] = array_filter(
                    $item->getEntityStructure(false, true),
                    function ($field) use ($fields) {
                        return in_array($field['key'], $fields);
                    }
                );
            }
        }

        return $result;
    }

    /**
     * @param $structure
     * @return array
     */
    private function getSpecificEntity($structure): array
    {
        return true ? array_keys($structure) : $structure;
    }

}
