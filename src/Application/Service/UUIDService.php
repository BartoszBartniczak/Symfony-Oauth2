<?php


namespace App\Application\Service;


use Symfony\Component\Uid\Uuid;

class UUIDService
{

    public function generate():string{
        return Uuid::v4()->toRfc4122();
    }

}
