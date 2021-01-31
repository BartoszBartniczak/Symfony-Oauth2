<?php


namespace App\Domain\Entity;


use App\Domain\Event\Event;

abstract class Entity
{

    /**
     * @var Event[]
     */
    protected array $events = [];

    /**
     * @return Event[]
     */
    final public function raiseEvents(): array{
        return $this->events;
    }

}
