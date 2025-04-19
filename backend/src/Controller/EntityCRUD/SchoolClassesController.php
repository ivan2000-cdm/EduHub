<?php

namespace App\Controller\EntityCRUD;

use App\Controller\EntityCRUD\Base\BaseEntityCRUDController;
use App\Entity\SchoolClasses;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SchoolClassesController extends BaseEntityCRUDController
{
    /**
     * Метод для получения класса сущности
     */
    protected function getClassEntity(): string
    {
        return SchoolClasses::class;
    }

    /**
     * Метод для создания сущности
     */
    #[Route('/api/school-classes', name: 'api_school_class_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return parent::create($request); // Вызов родительского метода
    }

    /**
     * Метод для обновления сущности
     */
    #[Route('/api/school-classes/{id}', name: 'api_school_class_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        return parent::update($id, $request); // Вызов родительского метода
    }

    /**
     * Метод для удаления сущности
     * @throws Exception
     */
    #[Route('/api/school-classes/{id}', name: 'api_school_class_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        return parent::delete($id); // Вызов родительского метода
    }

    /**
     * Метод для получения всех записей
     * @throws Exception
     */
    #[Route('/api/school-classes', name: 'api_school_classes', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        return parent::getAll(); // Вызов родительского метода
    }

    /**
     * Метод для получения записи по id
     * @throws Exception
     */
    #[Route('/api/school-classes/{id}', name: 'api_school_class_show', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {
        return parent::getById($id); // Вызов родительского метода
    }
}
