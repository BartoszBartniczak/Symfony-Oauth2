<?php


namespace App\Infrastructure\Symfony\Exception;


use App\Infrastructure\Exception\InvalidArgumentException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends InvalidArgumentException
{

    private ConstraintViolationListInterface $constraintViolationList;

    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        parent::__construct();
        $this->constraintViolationList = $constraintViolationList;
    }

    public function getConstraintViolationList(): ConstraintViolationListInterface
    {
        return $this->constraintViolationList;
    }

}
