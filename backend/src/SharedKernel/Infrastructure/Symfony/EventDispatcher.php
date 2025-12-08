<?php

namespace App\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Application\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

final readonly class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private SymfonyEventDispatcherInterface $eventDispatcher
    )
    {
    }

    public function dispatch(object $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }
}
