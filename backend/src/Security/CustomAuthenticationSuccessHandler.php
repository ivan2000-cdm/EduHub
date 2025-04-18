<?php

namespace App\Security;

use JetBrains\PhpStorm\NoReturn;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private JWTTokenManagerInterface $jwtManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorage)
    {
        $this->jwtManager = $jwtManager;
        $this->tokenStorage = $tokenStorage;
    }

    // Обратите внимание на ?Response в возвращаемом типе
    #[NoReturn] public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        // Получаем пользователя через токен
        $user = $this->tokenStorage->getToken()->getUser();

        // Генерируем JWT токен
        $jwt = $this->jwtManager->create($user);

        // Возвращаем его в формате JSON
        return new JsonResponse(['token' => $jwt]);
    }
}
