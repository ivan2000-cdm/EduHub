<?php

namespace App\Controller;

use App\AppCore\Exceptions\CustomException;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\JWTTokenService;
use App\Controller\Core\CoreController;
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

    /**
     * @throws CustomException
     */
    #[RouteAttribute('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['username']) || empty($data['password'])) {
            return $this->error('Пожалуйста, укажите логин и пароль.', Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(['username' => $data['username']]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->error('Неверные учетные данные.', Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtTokenService->create($user);

        return $this->json(['token' => $token]);
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