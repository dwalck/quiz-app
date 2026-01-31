<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

trait RecordsDomainEventsTrait
{
    /**
     * @var object[]
     */
    protected array $events = [];

    protected function recordEvent(object $event): void
    {
        $this->events[] = $event;
    }

    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
