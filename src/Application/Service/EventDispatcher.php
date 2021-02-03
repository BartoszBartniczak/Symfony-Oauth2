<?php


namespace App\Application\Service;


use App\Domain\Event\Event;

interface EventDispatcher
{

    public function dispatch(Event $event):void;

}
