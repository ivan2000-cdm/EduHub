<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Создание таблицы классов для справочника.
 */
final class Version20250419064211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы: Классы';
    }

    // Применение миграции:
    // php bin/console doctrine:migrations:execute --up 'DoctrineMigrations\Version20250419064211' --no-interaction
    public function up(Schema $schema): void
    {
        $this->addSql(
            "
            CREATE TABLE IF NOT EXISTS public.school_classes
            (
                id serial PRIMARY KEY,
                name character varying(10) NOT NULL,
                deleted boolean NOT NULL DEFAULT false
            );
        "
        );

        $this->addSql("COMMENT ON TABLE public.school_classes IS 'Справочник: Классы'");
        $this->addSql("COMMENT ON COLUMN public.school_classes.id IS 'Уникальный идентификатор'");
        $this->addSql("COMMENT ON COLUMN public.school_classes.name IS 'Название класса'");
        $this->addSql("COMMENT ON COLUMN public.school_classes.deleted IS 'Удаление'");

        $this->addSql('ALTER TABLE public.school_classes OWNER TO eduhub_user;');
    }

    // Откат миграции:
    // php bin/console doctrine:migrations:execute --down 'DoctrineMigrations\Version20250419064211' --no-interaction
    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE IF EXISTS public.school_classes;");
    }
}
