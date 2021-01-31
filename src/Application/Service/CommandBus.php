<?php


namespace App\Application\Service;


use App\Application\Command\Command;

interface CommandBus
{

    public function execute(Command $command):void;

}
