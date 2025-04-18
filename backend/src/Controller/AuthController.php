<?php

namespace App\Controller;

use App\AppCore\Exceptions\CustomException;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\JWTTokenService;
use App\Controller\Core\CoreController;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route as RouteAttribute;

class AuthController extends CoreController
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenService $jwtTokenService;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenService $jwtTokenService
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->jwtTokenService = $jwtTokenService;
    }

    #[NoReturn] #[RouteAttribute('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return new JsonResponse(['token' => 'jwt_token']); // Возвращаем токен
    }

    /**
     * @throws CustomException
     */
    #[RouteAttribute('/api/info', name: 'api_info', methods: ['GET'])]
    public function info(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->error('Неавторизованный доступ', Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'email' => $user->getEmail(),
            'created_at' => $user->getCreated()->format('Y-m-d H:i:s'),
        ]);
    }
}