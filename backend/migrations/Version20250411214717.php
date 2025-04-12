<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Создание таблицы пользователей с улучшением миграции.
 */
final class Version20250411214717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы пользователей с дефолтным значением для поля created';
    }

    // Применение миграции:
    // php bin/console doctrine:migrations:execute --up 'DoctrineMigrations\Version20250411214717' --no-interaction
    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE public.users
            (
                id BIGSERIAL NOT NULL,
                username VARCHAR(255) UNIQUE NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                roles VARCHAR(255)[] NOT NULL,
                created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            );
        ");
        $this->addSql("ALTER TABLE public.users OWNER TO eduhub_user;");
        $this->addSql("COMMENT ON TABLE public.users IS 'Таблица для хранения информации о пользователях';");
        $this->addSql("COMMENT ON COLUMN public.users.id IS 'Уникальный идентификатор пользователя';");
        $this->addSql("COMMENT ON COLUMN public.users.username IS 'Логин пользователя';");
        $this->addSql("COMMENT ON COLUMN public.users.email IS 'Электронная почта';");
        $this->addSql("COMMENT ON COLUMN public.users.password IS 'Пароль пользователя';");
        $this->addSql("COMMENT ON COLUMN public.users.roles IS 'Роли пользователя';");
        $this->addSql("COMMENT ON COLUMN public.users.created IS 'Дата создания пользователя';");
    }

    // Отмена миграции:
    // php bin/console doctrine:migrations:execute --down 'DoctrineMigrations\Version20250411214717' --no-interaction
    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE public.users;");
    }
}
