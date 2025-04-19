<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/api/menu', name: 'api_menu', methods: ['GET'])]
    public function getMenu(): JsonResponse
    {
        $menu = [
            [
                'title' => 'Главная',
                'path' => '/',
                'children' => []
            ],
            [
                'title' => 'Справочники',
                'path' => '/directories',
                'children' => [
                    [
                        'title' => 'Пользователи',
                        'path' => '/directories/users',
                    ],
                    [
                        'title' => 'Классы',
                        'path' => '/directories/classes',
                    ]
                ]
            ],
            [
                'title' => 'Настройки',
                'path' => '/settings',
                'children' => []
            ]
        ];

        return $this->json($menu);
    }
}