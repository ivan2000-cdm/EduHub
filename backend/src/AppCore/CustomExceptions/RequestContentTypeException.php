<?php


namespace App\AppCore\CustomExceptions;


class RequestContentTypeException extends \Exception
{
    public function __construct()
    {
        $message = 'Принимается только json дата';
        parent::__construct($message, 400, null);
    }
}
