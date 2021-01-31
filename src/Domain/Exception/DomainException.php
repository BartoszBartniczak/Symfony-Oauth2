<?php


namespace App\Domain\Exception;


use Throwable;

class DomainException extends \DomainException
{

    public function __construct($message = "", Throwable $previous = null, $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }

}
