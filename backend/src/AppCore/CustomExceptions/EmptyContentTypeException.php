<?php


namespace App\AppCore\CustomExceptions;


class EmptyContentTypeException extends \Exception
{
    public function __construct()
    {
        $message = 'Полученные данные пусты';
        parent::__construct($message, 400, null);
    }
}
