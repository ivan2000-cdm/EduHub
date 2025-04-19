<?php


namespace App\Interfaces;


interface EntityInterface
{
    public function getEntityStructure(bool $flag = false, bool $exclude_field = false): array;

}
