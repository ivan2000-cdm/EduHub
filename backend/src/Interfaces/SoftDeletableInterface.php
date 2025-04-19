<?php

namespace App\Interfaces;

interface SoftDeletableInterface
{
    public function setDeleted(bool $deleted): void;
    public function isDeleted(): bool;
}
