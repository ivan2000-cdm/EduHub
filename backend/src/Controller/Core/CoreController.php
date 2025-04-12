<?php

namespace App\Controller\Core;

use App\AppCore\Exceptions\CustomException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CoreController extends AbstractController
{
    /**
     * Стандартная отправка успешного ответа
     * @return JsonResponse
     */
    public function success(): JsonResponse
    {
        return $this->json(['success' => true]);
    }

    /**
     * Стандартная отправка ошибки
     *
     * @param string|null $message
     * @param int|null $code
     * @param Exception|CustomException|null $e
     * @return mixed
     * @throws CustomException
     */
    public function error(?string $message = 'Произошла ошибка', ?int $code = 500, Exception|CustomException $e = null): mixed
    {
        if ($e instanceof CustomException) {
            $code = $e->getCode();
        }
        throw new CustomException($message, $code, $e);
    }

    /**
     * Стандартный метод для возврата данных
     * @param array $data
     * @return JsonResponse
     */
    public function jsonResponse(array $data): JsonResponse
    {
        return $this->json($data);
    }
}
