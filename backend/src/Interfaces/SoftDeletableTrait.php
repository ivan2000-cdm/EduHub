<?php

namespace App\Traits;

trait SoftDeletableTrait
{
    protected bool $deleted = false;

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }
}
