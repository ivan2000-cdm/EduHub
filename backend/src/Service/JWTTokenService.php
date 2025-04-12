<?php

namespace App\Service;

use App\Entity\User;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JWTTokenService
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function create(User $user): string
    {
        // Получаем секретный ключ из параметров
        $key = $this->params->get('JWT_SECRET_KEY');
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  // Токен действует 1 час

        // Создаем полезную нагрузку токена (payload)
        $payload = [
            'iat' => $issuedAt,   // Время выпуска
            'exp' => $expirationTime, // Время истечения
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ];

        // Кодируем и возвращаем JWT токен
        return JWT::encode($payload, $key);
    }
}
