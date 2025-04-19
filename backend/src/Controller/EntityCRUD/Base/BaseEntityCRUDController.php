<?php

namespace App\Controller\EntityCRUD\Base;

use App\AbstractClasses\Entity;
use App\AppCore\CustomExceptions\EmptyContentTypeException;
use App\AppCore\CustomExceptions\RequestContentTypeException;
use App\Controller\CoreControllers\CoreController;
use App\Models\SimpleModel;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

abstract class BaseEntityCRUDController extends CoreController
{
    protected const ERROR_ENTITY_NOT_FOUND = 'entity_not_found';

    protected const RESPONSE_CREATE = 'create';
    protected const RESPONSE_UPDATE = 'update';
    protected const RESPONSE_DELETE = 'delete';

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Возвращает структуру сущности
     * @return JsonResponse
     * @throws Exception
     */
    public function getEntityStructure(): JsonResponse
    {
        $classEntity = $this->getClassEntity();
        /** @var Entity $entity */
        $entity = new $classEntity();
        $entityStructure = $entity->getEntityStructure();
        return $this->json($entityStructure);
    }

    /**
     * Сущность, с которой будет происходить работа
     * @return string
     */
    protected abstract function getClassEntity(): string;

    /**
     * Получить все записи
     * @return JsonResponse
     * @throws Exception
     */
    public function getAll(): JsonResponse
    {
        $entities = $this->entityManager->getRepository($this->getClassEntity())->findAll();
        if (empty($entities)) {
            $class_name = $this->getClassEntity();
            return $this->json([
                'data' => [],
                'entityStructure' => (new $class_name())->getEntityStructure()
            ]);
        }
        $entities = $this->entityArrayToJson($entities);
        return JsonResponse::fromJsonString($entities);
    }

    /**
     * Получить запись по id
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function getById(int $id): JsonResponse
    {
        $entity = $this->getEntity($id);
        $json = $this->entityToJson($entity);
        return JsonResponse::fromJsonString($json);
    }

    /**
     * Получить сущность по id
     * @param int $id
     * @return Entity
     * @throws Exception
     */
    protected function getEntity(int $id): Entity
    {
        /** @var Entity $entity */
        $entity = $this->entityManager->getRepository($this->getClassEntity())->find($id);
        if (empty($entity)) {
            throw new Exception($this->getMessage(self::ERROR_ENTITY_NOT_FOUND), 400);
        }
        return $entity;
    }

    /**
     * Сообщение из списка
     * @param string $key
     * @return string
     */
    protected function getMessage(string $key): string
    {
        return $this->getMessageTexts()[$key] ?? 'Unknown message';
    }

    /**
     * Список всех возможных сообщений
     * @return string[]
     */
    protected function getMessageTexts(): array
    {
        return [
            self::ERROR_ENTITY_NOT_FOUND => 'Сущность не найдена!',
            self::RESPONSE_CREATE => 'Данные успешно сохранены.',
            self::RESPONSE_UPDATE => 'Данные успешно обновлены.',
            self::RESPONSE_DELETE => 'Данные успешно удалены.'
        ];
    }

    /**
     * Создать запись
     * @param Request $request
     * @return JsonResponse
     * @throws EmptyContentTypeException
     * @throws RequestContentTypeException
     */
    public function create(Request $request): JsonResponse
    {
        $formData = $this->getDataRequest($request);
        $entity = $this->createEntity($formData);
        return $this->json(['response' => $entity->getId()], 200, $this->headers);
    }

    /**
     * Создать сущность
     * @param array $formData
     * @param bool $flush
     * @return Entity
     */
    protected function createEntity(array $formData, bool $flush = true): Entity
    {
        $classEntity = $this->getClassEntity();
        $classModel = $this->getClassModel();
        $model = new $classModel(new $classEntity(), $this->entityManager, $flush);
        return $model->saveModel($formData, true);
    }

    /**
     * Модель, работающая с сущностью
     * @return string
     */
    protected function getClassModel(): string
    {
        return SimpleModel::class;
    }

    /**
     * Изменить запись
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws EmptyContentTypeException
     * @throws RequestContentTypeException
     * @throws Exception
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $formData = $this->getDataRequest($request);
        $entity = $this->getEntity($id);
        $this->updateEntity($formData, $entity);
        return $this->json(['response' => $this->getMessage(self::RESPONSE_UPDATE)], 200, $this->headers);
    }

    /**
     * Обновить сущность
     * @param array $formData
     * @param Entity $entity
     * @param bool $flush
     * @return Entity
     */
    protected function updateEntity(array $formData, Entity $entity, bool $flush = true): Entity
    {
        $classModel = $this->getClassModel();
        $model = new $classModel($entity, $this->entityManager, $flush);
        return $model->saveModel($formData, false);
    }

    /**
     * Удалить запись
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(int $id): JsonResponse
    {
        $entity = $this->getEntity($id);

        if (method_exists($entity, 'setDeleted')) {
            $entity->setDeleted(true);
            $this->entityManager->flush();
            return $this->json(['response' => $this->getMessage(self::RESPONSE_DELETE)], 200, $this->headers);
        }

        return $this->json([
            'error' => 'Удаление не поддерживается для данной сущности (нет метода setDeleted).'
        ], 400);
    }

    /**
     * Создать/обновить сущность
     * @param array $formData
     * @param Entity|null $entity
     * @param bool $flush
     * @return Entity
     */
    protected function saveEntity(array $formData, ?Entity $entity = null, bool $flush = true): Entity
    {
        $classModel = $this->getClassModel();
        if ($entity) {
            $model = new $classModel($entity, $this->entityManager, $flush);
            return $model->saveModel($formData, false);
        }

        $classEntity = $this->getClassEntity();
        $model = new $classModel(new $classEntity(), $this->entityManager, $flush);
        return $model->saveModel($formData, true);
    }
}
