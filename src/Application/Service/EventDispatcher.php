<?php


namespace App\Application\Service;


use App\Domain\Event\Event;

interface EventDispatcher
{

    /**
     * @param Event[] $events
     */
    public function dispatch(array $events):void;

}
