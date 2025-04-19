<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Класс
 *
 * @ORM\Table(name="school_classes")
 * @ORM\Entity
 */
#[ORM\Entity]
#[ORM\Table(name: 'school_classes')]
class SchoolClasses
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment": "Уникальный идентификатор"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", options: ["comment" => "Уникальный идентификатор"])]
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=false, options={"comment": "Название класса"})
     */
    #[ORM\Column(type: "string", length: 10, nullable: false, options: ["comment" => "Название класса"])]
    private string $name;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"comment": "Удалено"})
     */
    #[ORM\Column(type: "boolean", options: ["comment" => "Удалено"])]
    private bool $deleted = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * Структура для автоматизации CRUD
     */
    public function getEntityStructure(): array
    {
        return [
            'id' => [
                'label' => 'ID',
                'required' => false,
                'readonly' => true,
                'show' => true,
                'type' => 'int',
                'key' => 'id'
            ],
            'name' => [
                'label' => 'Название класса',
                'required' => true,
                'readonly' => false,
                'show' => true,
                'type' => 'string',
                'key' => 'name'
            ],
            'deleted' => [
                'label' => 'Удалено',
                'required' => false,
                'readonly' => false,
                'show' => false,
                'type' => 'boolean',
                'key' => 'deleted'
            ]
        ];
    }

    protected function getExcludedFields(): array
    {
        return [];
    }
}
