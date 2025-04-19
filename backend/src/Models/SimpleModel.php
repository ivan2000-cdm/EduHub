<?php


namespace App\Models;


use App\AbstractClasses\Models;

class SimpleModel extends Models
{

    protected function validateAdditionally(array $data): bool
    {
        return true;
    }

    protected function validateEntites(array $data): bool
    {
        return true;
    }
}
